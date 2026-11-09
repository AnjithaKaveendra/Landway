function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[char]));
}
function escapedRecord(record) {
  return Object.fromEntries(Object.entries(record).map(([key, value]) => [key,
    Array.isArray(value) ? value.map(escapeHtml) : typeof value === 'string' ? escapeHtml(value) : value]));
}
// Admin Dashboard JavaScript - Database Version

let currentAccountId = null;
let currentPropertyId = null;
let currentInquiryId = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  loadAllData();
  setupMenuListeners();
});

// Load all data from database
function loadAllData() {
  loadAccounts();
  loadProperties();
  loadInquiries();
  loadEmployees();
  updateStats();
}

// Load Accounts
function loadAccounts() {
  fetch('api-users.php?action=get-all')
    .then(response => response.json())
    .then(data => {
      const tableBody = document.getElementById('accountsTableBody');
      
      if (data.status === 'success' && data.data.length > 0) {
        tableBody.innerHTML = '';
        data.data.forEach((account, index) => {
          account = escapedRecord(account);
          const registrationDate = new Date(account.registrationDate).toLocaleDateString();
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${account.id}</td>
            <td>${account.firstName}</td>
            <td>${account.lastName}</td>
            <td>${account.email}</td>
            <td>${account.phone}</td>
            <td>${registrationDate}</td>
            <td>
              <button class="btn btn-sm btn-info" onclick="viewAccount(${account.id})" title="View Details">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteAccountConfirm(${account.id})" title="Delete">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          `;
          tableBody.appendChild(row);
        });
      } else {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No accounts found</td></tr>';
      }
    })
    .catch(error => console.error('Error loading accounts:', error));
}

// View Account Details
function viewAccount(id) {
  fetch(`api-users.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        const account = escapedRecord(data.data);
        currentAccountId = id;
        
        const detailsHtml = `
          <ul class="details-list">
            <li><span class="details-label">First Name:</span><span class="details-value">${account.firstName}</span></li>
            <li><span class="details-label">Last Name:</span><span class="details-value">${account.lastName}</span></li>
            <li><span class="details-label">Email:</span><span class="details-value">${account.email}</span></li>
            <li><span class="details-label">Phone:</span><span class="details-value">${account.phone}</span></li>
            <li><span class="details-label">Registration Date:</span><span class="details-value">${new Date(account.registrationDate).toLocaleDateString()}</span></li>
          </ul>
        `;
        
        document.getElementById('accountDetailsBody').innerHTML = detailsHtml;
        $('#viewAccountModal').modal('show');
      }
    })
    .catch(error => console.error('Error viewing account:', error));
}

// Delete Account
function deleteAccountConfirm(id) {
  if (confirm('Are you sure you want to delete this account?')) {
    fetch('api-users.php?action=delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        alert('Account deleted successfully');
        loadAccounts();
        updateStats();
        $('#viewAccountModal').modal('hide');
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error deleting account:', error));
  }
}

function deleteAccount() {
  if (currentAccountId) {
    deleteAccountConfirm(currentAccountId);
  }
}

// Load Properties
function loadProperties() {
  fetch('api-properties.php?action=get-all')
    .then(response => response.json())
    .then(data => {
      const tableBody = document.getElementById('propertiesTableBody');
      
      if (data.status === 'success' && data.data.length > 0) {
        tableBody.innerHTML = '';
        data.data.forEach((property) => {
          property = escapedRecord(property);
          const statusText = property.propertyStatus === 'sale' ? 'For Sale' : 
                             (property.propertyStatus === 'rent' ? 'For Rent' : 'For Lease');
          const location = property.propertyCity || property.propertyAddress || 'N/A';
          const price = property.propertyPrice || 'N/A';
          const beds = property.propertyBedrooms || '-';
          const baths = property.propertyBathrooms || '-';
          const area = property.propertyArea || '-';
          
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${property.id}</td>
            <td>${property.propertyTitle}</td>
            <td>${property.propertyType}</td>
            <td>${location}</td>
            <td>${property.propertyCurrency} ${price}</td>
            <td>${beds}</td>
            <td>${baths}</td>
            <td>${area}</td>
            <td><span class="status-badge status-${property.propertyStatus}">${statusText}</span></td>
            <td>
              <button class="btn btn-sm btn-info" onclick="viewProperty(${property.id})" title="View Details">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deletePropertyConfirm(${property.id})" title="Delete">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          `;
          tableBody.appendChild(row);
        });
      } else {
        tableBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No properties found</td></tr>';
      }
    })
    .catch(error => console.error('Error loading properties:', error));
}

// View Property Details
function viewProperty(id) {
  fetch(`api-properties.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        const property = escapedRecord(data.data);
        currentPropertyId = id;
        
        const statusText = property.propertyStatus === 'sale' ? 'For Sale' : 
                           (property.propertyStatus === 'rent' ? 'For Rent' : 'For Lease');
        
        const detailsHtml = `
          <ul class="details-list">
            <li><span class="details-label">Title:</span><span class="details-value">${property.propertyTitle}</span></li>
            <li><span class="details-label">Type:</span><span class="details-value">${property.propertyType}</span></li>
            <li><span class="details-label">Status:</span><span class="details-value"><span class="status-badge status-${property.propertyStatus}">${statusText}</span></span></li>
            <li><span class="details-label">Price:</span><span class="details-value">${property.propertyCurrency} ${property.propertyPrice}</span></li>
            <li><span class="details-label">Address:</span><span class="details-value">${property.propertyAddress}</span></li>
            <li><span class="details-label">City:</span><span class="details-value">${property.propertyCity}</span></li>
            <li><span class="details-label">State/Province:</span><span class="details-value">${property.propertyState || 'N/A'}</span></li>
            <li><span class="details-label">ZIP Code:</span><span class="details-value">${property.propertyZipCode || 'N/A'}</span></li>
            <li><span class="details-label">Country:</span><span class="details-value">${property.propertyCountry || 'N/A'}</span></li>
            <li><span class="details-label">Area:</span><span class="details-value">${property.propertyArea || 'N/A'} sq ft</span></li>
            <li><span class="details-label">Bedrooms:</span><span class="details-value">${property.propertyBedrooms || 'N/A'}</span></li>
            <li><span class="details-label">Bathrooms:</span><span class="details-value">${property.propertyBathrooms || 'N/A'}</span></li>
            <li><span class="details-label">Year Built:</span><span class="details-value">${property.propertyYearBuilt || 'N/A'}</span></li>
            <li><span class="details-label">Garage/Parking:</span><span class="details-value">${property.propertyGarage || 'N/A'}</span></li>
            <li><span class="details-label">Features:</span><span class="details-value">${property.features && property.features.length > 0 ? property.features.join(', ') : 'N/A'}</span></li>
            <li><span class="details-label">Description:</span><span class="details-value">${property.propertyDescription || 'N/A'}</span></li>
            <li><span class="details-label">Owner Name:</span><span class="details-value">${property.ownerName || 'N/A'}</span></li>
            <li><span class="details-label">Owner Email:</span><span class="details-value">${property.ownerEmail || 'N/A'}</span></li>
            <li><span class="details-label">Owner Phone:</span><span class="details-value">${property.ownerPhone || 'N/A'}</span></li>
            <li><span class="details-label">Listed Date:</span><span class="details-value">${new Date(property.listedDate).toLocaleDateString()}</span></li>
          </ul>
        `;
        
        document.getElementById('propertyDetailsBody').innerHTML = detailsHtml;
        $('#viewPropertyModal').modal('show');
      }
    })
    .catch(error => console.error('Error viewing property:', error));
}

// Delete Property
function deletePropertyConfirm(id) {
  if (confirm('Are you sure you want to delete this property?')) {
    fetch('api-properties.php?action=delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        alert('Property deleted successfully');
        loadProperties();
        updateStats();
        $('#viewPropertyModal').modal('hide');
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error deleting property:', error));
  }
}

function deleteProperty() {
  if (currentPropertyId) {
    deletePropertyConfirm(currentPropertyId);
  }
}

// Load Inquiries
function loadInquiries() {
  fetch('api-inquiries.php?action=get-all')
    .then(response => response.json())
    .then(data => {
      const tableBody = document.getElementById('inquiriesTableBody');
      
      if (data.status === 'success' && data.data.length > 0) {
        tableBody.innerHTML = '';
        data.data.forEach((inquiry) => {
          inquiry = escapedRecord(inquiry);
          const inquiryDate = new Date(inquiry.inquiryDate).toLocaleDateString();
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${inquiry.id}</td>
            <td>${inquiry.contactName}</td>
            <td>${inquiry.contactEmail}</td>
            <td>${inquiry.contactPhone}</td>
            <td>${inquiry.contactSubject}</td>
            <td>${inquiry.contactPropertyType || 'N/A'}</td>
            <td>${inquiryDate}</td>
            <td>
              <button class="btn btn-sm btn-info" onclick="viewInquiry(${inquiry.id})" title="View Details">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteInquiryConfirm(${inquiry.id})" title="Delete">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          `;
          tableBody.appendChild(row);
        });
      } else {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No inquiries found</td></tr>';
      }
    })
    .catch(error => console.error('Error loading inquiries:', error));
}

// View Inquiry Details
function viewInquiry(id) {
  fetch(`api-inquiries.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        const inquiry = escapedRecord(data.data);
        currentInquiryId = id;
        
        const detailsHtml = `
          <ul class="details-list">
            <li><span class="details-label">Name:</span><span class="details-value">${inquiry.contactName}</span></li>
            <li><span class="details-label">Email:</span><span class="details-value">${inquiry.contactEmail}</span></li>
            <li><span class="details-label">Phone:</span><span class="details-value">${inquiry.contactPhone}</span></li>
            <li><span class="details-label">Subject:</span><span class="details-value">${inquiry.contactSubject}</span></li>
            <li><span class="details-label">Property Type:</span><span class="details-value">${inquiry.contactPropertyType || 'N/A'}</span></li>
            <li><span class="details-label">Message:</span><span class="details-value">${inquiry.contactMessageText}</span></li>
            <li><span class="details-label">Inquiry Date:</span><span class="details-value">${new Date(inquiry.inquiryDate).toLocaleDateString()}</span></li>
          </ul>
        `;
        
        document.getElementById('inquiryDetailsBody').innerHTML = detailsHtml;
        $('#viewInquiryModal').modal('show');
      }
    })
    .catch(error => console.error('Error viewing inquiry:', error));
}

// Delete Inquiry
function deleteInquiryConfirm(id) {
  if (confirm('Are you sure you want to delete this inquiry?')) {
    fetch('api-inquiries.php?action=delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        alert('Inquiry deleted successfully');
        loadInquiries();
        updateStats();
        $('#viewInquiryModal').modal('hide');
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error deleting inquiry:', error));
  }
}

function deleteInquiry() {
  if (currentInquiryId) {
    deleteInquiryConfirm(currentInquiryId);
  }
}

// Load Employees
function loadEmployees() {
  fetch('api-employees.php?action=get-all')
    .then(response => response.json())
    .then(data => {
      const tableBody = document.getElementById('employeesTableBody');
      
      if (data.status === 'success' && data.data.length > 0) {
        tableBody.innerHTML = '';
        data.data.forEach((employee) => {
          employee = escapedRecord(employee);
          const hireDate = employee.hireDate ? new Date(employee.hireDate).toLocaleDateString() : 'N/A';
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${employee.id}</td>
            <td>${employee.employeeId}</td>
            <td>${employee.firstName}</td>
            <td>${employee.lastName}</td>
            <td>${employee.email}</td>
            <td>${employee.phone}</td>
            <td>${employee.position}</td>
            <td>${hireDate}</td>
            <td>
              <button class="btn btn-sm btn-info" onclick="viewEmployee(${employee.id})" title="View Details">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteEmployeeConfirm(${employee.id})" title="Delete">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          `;
          tableBody.appendChild(row);
        });
      } else {
        tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No employees found. Click "Add Employee" to add staff.</td></tr>';
      }
    })
    .catch(error => console.error('Error loading employees:', error));
}

// View Employee Details
function viewEmployee(id) {
  fetch(`api-employees.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        const employee = escapedRecord(data.data);
        const hireDate = employee.hireDate ? new Date(employee.hireDate).toLocaleDateString() : 'N/A';
        
        const detailsHtml = `
          <ul class="details-list">
            <li><span class="details-label">Employee ID:</span><span class="details-value">${employee.employeeId}</span></li>
            <li><span class="details-label">First Name:</span><span class="details-value">${employee.firstName}</span></li>
            <li><span class="details-label">Last Name:</span><span class="details-value">${employee.lastName}</span></li>
            <li><span class="details-label">Email:</span><span class="details-value">${employee.email}</span></li>
            <li><span class="details-label">Phone:</span><span class="details-value">${employee.phone}</span></li>
            <li><span class="details-label">Position:</span><span class="details-value">${employee.position}</span></li>
            <li><span class="details-label">Hire Date:</span><span class="details-value">${hireDate}</span></li>
          </ul>
        `;
        
        alert(detailsHtml.replace(/<[^>]*>/g, '\n'));
      }
    })
    .catch(error => console.error('Error viewing employee:', error));
}

// Delete Employee
function deleteEmployeeConfirm(id) {
  if (confirm('Are you sure you want to delete this employee?')) {
    fetch('api-employees.php?action=delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        alert('Employee deleted successfully');
        loadEmployees();
        updateStats();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error deleting employee:', error));
  }
}

// Add Employee
function handleAddEmployee(event) {
  event.preventDefault();

  const firstName = document.getElementById('empFirstName').value.trim();
  const lastName = document.getElementById('empLastName').value.trim();
  const email = document.getElementById('empEmail').value.trim();
  const phone = document.getElementById('empPhone').value.trim();
  const position = document.getElementById('empPosition').value.trim();
  const employeeId = document.getElementById('empEmployeeId').value.trim();

  if (!firstName || !lastName || !email || !employeeId || !position) {
    showEmployeeMessage('Please fill in all required fields', 'alert-danger');
    return;
  }

  fetch('api-employees.php?action=create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      firstName, lastName, email, phone, position, employeeId
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      showEmployeeMessage('Employee added successfully!', 'alert-success');
      document.getElementById('addEmployeeForm').reset();
      setTimeout(() => {
        $('#addEmployeeModal').modal('hide');
        loadEmployees();
        updateStats();
      }, 1500);
    } else {
      showEmployeeMessage(data.message, 'alert-danger');
    }
  })
  .catch(error => {
    showEmployeeMessage('Error: ' + error.message, 'alert-danger');
  });
}

function showEmployeeMessage(message, alertClass) {
  const messageDiv = document.getElementById('employeeMessage');
  messageDiv.textContent = message;
  messageDiv.className = 'alert ' + alertClass;
  messageDiv.style.display = 'block';
}

// Change Password
function handleChangePassword(event) {
  event.preventDefault();

  const currentPassword = document.getElementById('currentPassword').value;
  const newPassword = document.getElementById('newPassword').value;
  const confirmNewPassword = document.getElementById('confirmNewPassword').value;

  if (!currentPassword || !newPassword || !confirmNewPassword) {
    showPasswordMessage('Please fill in all fields', 'alert-danger');
    return;
  }

  if (newPassword !== confirmNewPassword) {
    showPasswordMessage('Passwords do not match', 'alert-danger');
    return;
  }

  if (newPassword.length < 8) {
    showPasswordMessage('New password must be at least 8 characters', 'alert-danger');
    return;
  }

  fetch('api-admin.php?action=change-password', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      currentPassword, newPassword
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      showPasswordMessage('Password changed successfully!', 'alert-success');
      document.getElementById('changePasswordForm').reset();
      setTimeout(() => {
        $('#changePasswordModal').modal('hide');
      }, 1500);
    } else {
      showPasswordMessage(data.message, 'alert-danger');
    }
  })
  .catch(error => {
    showPasswordMessage('Error: ' + error.message, 'alert-danger');
  });
}

function showPasswordMessage(message, alertClass) {
  const messageDiv = document.getElementById('passwordMessage');
  messageDiv.textContent = message;
  messageDiv.className = 'alert ' + alertClass;
  messageDiv.style.display = 'block';
}

// Update Statistics
function updateStats() {
  Promise.all([
    fetch('api-users.php?action=get-all').then(r => r.json()),
    fetch('api-properties.php?action=get-all').then(r => r.json()),
    fetch('api-inquiries.php?action=get-all').then(r => r.json()),
    fetch('api-employees.php?action=get-all').then(r => r.json())
  ])
  .then(([users, properties, inquiries, employees]) => {
    document.getElementById('totalAccounts').textContent = users.data ? users.data.length : 0;
    document.getElementById('totalProperties').textContent = properties.data ? properties.data.length : 0;
    document.getElementById('totalInquiries').textContent = inquiries.data ? inquiries.data.length : 0;
    document.getElementById('totalEmployees').textContent = employees.data ? employees.data.length : 0;
  })
  .catch(error => console.error('Error updating stats:', error));
}

// Setup Sidebar Menu
function setupMenuListeners() {
  document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
      const section = this.getAttribute('data-section');
      showSection(section);
      
      document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      
      document.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
      document.getElementById(section + '-section').classList.add('active');
    });
  });
}

function showSection(section) {
  const sections = document.querySelectorAll('.dashboard-section');
  sections.forEach(s => s.style.display = 'none');
  
  const targetSection = document.getElementById(section + '-section');
  if (targetSection) {
    targetSection.style.display = 'block';
  }
}

// Logout
function logoutAdmin() {
  if (confirm('Are you sure you want to logout?')) {
    fetch('api-admin.php?action=logout', {
      method: 'POST'
    })
    .then(response => response.json()).then(data => {
      if (data.status !== 'success') throw new Error(data.message);
      window.location.href = 'login.php';
    });
  }
}
