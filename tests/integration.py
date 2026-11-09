"""Exercise the real PHP/MySQL application in a new, disposable database.

Run: python tests/integration.py
Optional: python tests/integration.py --browser (requires Python Playwright + Chromium)
PHP_BIN and DB_* environment variables override local XAMPP defaults.
"""
from pathlib import Path
import http.cookiejar
import json
import os
import re
import secrets
import shutil
import socket
import subprocess
import sys
import tempfile
import time
import urllib.error
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
APP = ROOT / 'Landway'
PHP = os.environ.get('PHP_BIN') or shutil.which('php') or 'C:/xampp/php/php.exe'
DATABASE = 'landway_test_' + secrets.token_hex(6)
PASSWORD = secrets.token_urlsafe(18)
ENV = {**os.environ, 'DB_NAME': DATABASE, 'ADMIN_EMAIL': 'admin@example.test', 'ADMIN_PASSWORD': PASSWORD}
checks = 0


def check(condition, message):
    global checks
    assert condition, message
    checks += 1


class Client:
    def __init__(self):
        self.opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
        self.token = ''

    def request(self, route, data=None, status=200, csrf=True, raw=None):
        headers = {}
        body = None
        if data is not None or raw is not None:
            body = raw if raw is not None else json.dumps(data).encode()
            headers['Content-Type'] = 'application/json'
            if csrf:
                headers['X-CSRF-Token'] = self.token
        try:
            response = self.opener.open(urllib.request.Request(BASE + route, data=body, headers=headers), timeout=10)
        except urllib.error.HTTPError as error:
            response = error
        text = response.read().decode('utf-8')
        check(response.status == status, f'{route}: expected {status}, got {response.status}: {text[:300]}')
        if 'application/json' in response.headers.get('Content-Type', ''):
            try:
                return json.loads(text)
            except json.JSONDecodeError:
                raise AssertionError(f'{route} returned invalid JSON: {text[:1000]}') from None
        return text

    def session(self):
        data = self.request('api-users.php?action=session')
        self.token = data['csrfToken']
        return data


def browser_checks():
    from playwright.sync_api import sync_playwright
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True)
        context = browser.new_context(viewport={'width': 1440, 'height': 1000})
        # Fonts are optional; the application itself needs no external service.
        context.route('https://fonts.googleapis.com/**', lambda route: route.abort())
        context.route('https://fonts.gstatic.com/**', lambda route: route.abort())
        page = context.new_page()
        errors = []
        page.on('pageerror', lambda error: errors.append(str(error)))
        page.goto(BASE + 'register.php')
        for field, value in {'firstName': 'Browser', 'lastName': 'Customer', 'email': 'browser@example.test', 'phone': '0771234567', 'password': PASSWORD, 'confirmPassword': PASSWORD}.items():
            page.locator('#' + field).fill(value)
        page.locator('button[type=submit]').click()
        page.wait_for_url('**/login.php')
        page.locator('#email').fill('browser@example.test')
        page.locator('#password').fill(PASSWORD)
        page.locator('button[type=submit]').click()
        page.wait_for_url('**/index.php')
        check(page.get_by_text('Welcome, Browser.').is_visible(), 'Customer login did not render server session')
        page.goto(BASE + 'add-property.php')
        for field, value in {'propertyTitle': 'Browser Listing', 'propertyDescription': 'Submitted through the actual property form.', 'propertyAddress': '14 Test Road', 'propertyCity': 'Colombo', 'propertyPrice': '200000', 'ownerName': 'Browser Customer', 'ownerEmail': 'browser@example.test', 'ownerPhone': '0771234567'}.items():
            page.locator('#' + field).fill(value)
        page.locator('#propertyType').select_option('house')
        page.locator('#propertyStatus').select_option('sale')
        page.locator('button[type=submit]').click()
        page.wait_for_url('**/properties.php')
        check(page.get_by_role('link', name='Browser Listing', exact=True).is_visible(), 'New property missing from public listing')
        page.locator('#propertyTypeFilter').select_option('land')
        check(not page.get_by_role('link', name='Browser Listing', exact=True).is_visible(), 'Property filter failed')
        page.locator('#propertyTypeFilter').select_option('all')
        page.get_by_role('link', name='Browser Listing', exact=True).click()
        page.locator('#saveListing').click()
        page.get_by_text('Property saved.', exact=True).wait_for()
        page.locator('#inquiryPhone').fill('0771234567')
        page.locator('#propertyInquiryForm button[type=submit]').click()
        page.get_by_text('Thank you. Your inquiry has been submitted to Landway.').wait_for()
        page.locator('#interestRate').fill('0')
        page.locator('#mortgageForm button[type=submit]').click()
        check('NaN' not in page.locator('#monthlyPayment').inner_text() and 'per month' in page.locator('#monthlyPayment').inner_text(), 'Zero-interest mortgage calculation failed')
        page.goto(BASE + 'saved-properties.php')
        check(page.get_by_role('link', name='Browser Listing', exact=True).last.is_visible(), 'Saved property was not persisted')
        page.goto(BASE + 'contact.php')
        for field, value in {'contactName': 'Browser Customer', 'contactEmail': 'browser@example.test', 'contactPhone': '0771234567', 'contactSubject': 'Browser Inquiry', 'contactMessageText': 'Sent from the contact page.'}.items():
            page.locator('#' + field).fill(value)
        page.locator('button[type=submit]').click()
        page.get_by_text('Message sent successfully! We will contact you soon.').wait_for()
        page.get_by_role('button', name='Logout', exact=True).click()
        page.wait_for_url('**/index.php')
        page.goto(BASE + 'login.php')
        page.locator('#email').fill('admin@example.test')
        page.locator('#password').fill(PASSWORD)
        page.locator('button[type=submit]').click()
        page.wait_for_url('**/admin-dashboard.php')
        page.locator('#accountsTableBody').get_by_text('browser@example.test', exact=True).wait_for()
        page.wait_for_function("document.getElementById('totalProperties').textContent === '5'")
        check(page.locator('#totalProperties').inner_text() == '5', 'Dashboard count does not match database')
        page.locator('[data-section=inquiries]').click()
        page.locator('#inquiriesTableBody').get_by_text('Browser Inquiry', exact=True).wait_for()
        page.locator('[data-section=properties]').click()
        page.locator('#propertiesTableBody').get_by_text('Browser Listing', exact=True).wait_for()
        page.locator('[data-section=employees]').click()
        page.locator('[data-target="#addEmployeeModal"]').click()
        for field, value in {'empFirstName': 'Browser', 'empLastName': 'Employee', 'empEmail': 'browser.employee@example.test', 'empPhone': '0771234567', 'empPosition': 'Agent', 'empEmployeeId': 'BROWSER-001'}.items():
            page.locator('#' + field).fill(value)
        page.locator('#addEmployeeForm button[type=submit]').click()
        page.locator('#employeesTableBody').get_by_text('browser.employee@example.test', exact=True).wait_for()
        page.set_viewport_size({'width': 390, 'height': 844})
        page.goto(BASE + 'properties.php')
        page.locator('.navbar-toggler').click()
        check(page.get_by_role('link', name='Contact', exact=True).first.is_visible(), 'Mobile navigation failed')
        check(not errors, 'Browser JavaScript errors: ' + repr(errors))
        browser.close()
    print('Browser flows passed: registration, customer/admin login, listing, filters, favorites, inquiries, mortgage, employees, and mobile navigation.')


with socket.socket() as sock:
    sock.bind(('127.0.0.1', 0))
    PORT = sock.getsockname()[1]
BASE = f'http://127.0.0.1:{PORT}/'
server = None
try:
    for _ in range(2):
        setup = subprocess.run([PHP, 'init-db.php', '--seed'], cwd=APP, env=ENV, capture_output=True, text=True)
        check(setup.returncode == 0, setup.stderr)
    with tempfile.TemporaryFile() as log:
        server = subprocess.Popen([PHP, '-S', f'127.0.0.1:{PORT}', '-t', str(APP)], env=ENV, stdout=log, stderr=log)
        guest = Client()
        for attempt in range(50):
            try:
                guest.session()
                break
            except urllib.error.URLError:
                time.sleep(0.1)
        else:
            raise RuntimeError('PHP server did not start')
        check(guest.request('test-connection.php')['status'] == 'success', 'Schema is not ready')
        listings = guest.request('api-properties.php?action=get-all')['data']
        check(len(listings) == 4, 'Repeatable setup duplicated seed properties')
        for page_name in ['index.php', 'about.php', 'policy.php', 'properties.php', 'login.php', 'register.php', 'contact.php', 'property-detail.php?id=1', 'Land%20Way/register.php']:
            html = guest.request(page_name)
            check('Fatal error' not in html and '<html' in html, 'Invalid page: ' + page_name)
            for asset in re.findall(r'(?:src|href)="([^"<>]+)"', html):
                if not asset.startswith(('http', '#', 'mailto:', 'tel:', '<', '?')) and '<?' not in asset:
                    local_path = APP / asset.split('?', 1)[0].split('#', 1)[0]
                    check(local_path.exists(), f'Missing local asset/link in {page_name}: {asset}')
        guest.request('property-detail.php?id=999999', status=404)
        guest.request('property-detail.php?id=bad', status=404)
        guest.request('api-users.php?action=get-all', status=401)
        guest.request('api-employees.php?action=get-all', status=401)
        guest.request('api-inquiries.php?action=get-all', status=401)
        guest.request('api-properties.php?action=create', {}, status=401)
        guest.request('api-users.php?action=register', {}, status=403, csrf=False)
        guest.request('api-users.php?action=register', status=405)
        guest.request('api-users.php?action=register', status=400, raw=b'{')
        account = {'firstName': 'Test <script>alert(1)</script>', 'lastName': "O'Neil", 'email': 'user@example.test', 'phone': '0771234567', 'password': PASSWORD}
        guest.request('api-users.php?action=register', {**account, 'email': 'invalid'}, status=422)
        user_id = guest.request('api-users.php?action=register', account, status=201)['id']
        guest.request('api-users.php?action=register', account, status=409)
        guest.request('api-admin.php?action=login', {'email': account['email'], 'password': PASSWORD}, status=401)
        guest.request('api-users.php?action=login', {'email': account['email'], 'password': 'bad-password'}, status=401)
        login = guest.request('api-users.php?action=login', {'email': account['email'], 'password': PASSWORD})
        check(login['user']['userId'] == user_id, 'Wrong authenticated user')
        check('&lt;script&gt;' in guest.request('index.php'), 'Session name was not HTML escaped')
        guest.request('api-employees.php?action=get-all', status=403)
        property_data = {'propertyTitle': 'Integration <script>alert(1)</script>', 'propertyType': 'house', 'propertyStatus': 'sale', 'propertyDescription': "A test listing with O'Neil's garden.", 'propertyAddress': '14 Test Road', 'propertyCity': 'Colombo', 'propertyPrice': '123456.78', 'propertyArea': '2500.50', 'propertyBedrooms': 4, 'propertyBathrooms': 2, 'propertyCurrency': 'LKR', 'features': ['Garden', 'Air Conditioning'], 'ownerName': 'Test User', 'ownerEmail': 'user@example.test', 'ownerPhone': '0771234567'}
        guest.request('api-properties.php?action=create', {**property_data, 'propertyPrice': -1}, status=422)
        property_id = guest.request('api-properties.php?action=create', property_data, status=201)['id']
        stored = guest.request(f'api-properties.php?action=get&id={property_id}')['data']
        check(float(stored['propertyPrice']) == 123456.78 and float(stored['propertyArea']) == 2500.5, 'Decimal values were truncated')
        check(stored['features'] == ['Garden', 'Air Conditioning'], 'Features did not round-trip')
        check('&lt;script&gt;' in guest.request(f'property-detail.php?id={property_id}'), 'Property was not HTML escaped')
        guest.request('api-properties.php?action=update', {'id': property_id, 'propertyTitle': 'Updated Listing'})
        guest.request('api-favorites.php?action=toggle', {'id': property_id})
        check(len(guest.request('api-favorites.php?action=get-all')['data']) == 1, 'Favorite not stored')
        check('Updated Listing' in guest.request('saved-properties.php'), 'Saved listing page missing favorite')
        inquiry_id = guest.request('api-inquiries.php?action=create', {'contactName': 'Test User', 'contactEmail': account['email'], 'contactSubject': 'Integration inquiry', 'contactMessageText': 'Is this property available?'}, status=201)['id']
        other = Client()
        other.session()
        other.request('api-users.php?action=register', {**account, 'email': 'other@example.test'}, status=201)
        other.request('api-users.php?action=login', {'email': 'other@example.test', 'password': PASSWORD})
        other.request('api-properties.php?action=update', {'id': property_id, 'propertyTitle': 'Unauthorized'}, status=403)
        other.request('api-properties.php?action=delete', {'id': property_id}, status=403)
        admin = Client()
        admin.session()
        admin.request('api-admin.php?action=login', {'email': 'admin@example.test', 'password': PASSWORD})
        check('password' not in admin.request('api-users.php?action=get-all')['data'][0], 'Password hashes leaked')
        employee = {'employeeId': 'TEST-001', 'firstName': 'Test', 'lastName': 'Employee', 'email': 'employee@example.test', 'phone': '0771234567', 'position': 'Agent'}
        employee_id = admin.request('api-employees.php?action=create', employee, status=201)['id']
        admin.request('api-employees.php?action=update', {'id': employee_id, 'position': 'Manager'})
        check(admin.request(f'api-employees.php?action=get&id={employee_id}')['data']['position'] == 'Manager', 'Employee update failed')
        admin.request('api-users.php?action=update', {'id': user_id, 'firstName': 'Updated', 'lastName': 'Customer', 'phone': '0771234567'})
        admin.request(f'api-inquiries.php?action=get&id={inquiry_id}')
        admin.request('api-inquiries.php?action=delete', {'id': inquiry_id})
        admin.request('api-employees.php?action=delete', {'id': employee_id})
        admin.request('api-properties.php?action=delete', {'id': property_id})
        check(not guest.request('api-favorites.php?action=get-all')['data'], 'Deleted property left a dangling favorite')
        admin.request('api-users.php?action=delete', {'id': user_id})
        admin.request('api-admin.php?action=change-password', {'currentPassword': PASSWORD, 'newPassword': PASSWORD + 'New'})
        admin.request('api-admin.php?action=logout', {})
        admin.request('api-users.php?action=get-all', status=401)
        admin.session()
        admin.request('api-admin.php?action=login', {'email': 'admin@example.test', 'password': PASSWORD}, status=401)
        admin.request('api-admin.php?action=login', {'email': 'admin@example.test', 'password': PASSWORD + 'New'})
        # Restore the test admin password for browser verification.
        admin.request('api-admin.php?action=change-password', {'currentPassword': PASSWORD + 'New', 'newPassword': PASSWORD})
        if '--browser' in sys.argv:
            browser_checks()
        print(f'Passed {checks} checks using PHP/MySQL and disposable database {DATABASE}.')
finally:
    if server:
        server.terminate()
        server.wait(timeout=10)
    # This name is generated above, never taken from the user's DB_NAME.
    assert re.fullmatch(r'landway_test_[0-9a-f]{12}', DATABASE)
    cleanup = "require 'settings.php'; mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); $db = new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT); $db->query('DROP DATABASE `" + DATABASE + "`');"
    result = subprocess.run([PHP, '-r', cleanup], cwd=APP, env=ENV, capture_output=True, text=True)
    if result.returncode:
        print('Could not remove the disposable test database:', DATABASE, file=sys.stderr)
