// Admin Dashboard JavaScript

let currentAccountId = null;
let currentPropertyId = null;
let currentInquiryId = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Check if user is authenticated as admin
  checkAdminAuthentication();
  
  // Load all data
  loadAllData();
  
  // Setup sidebar menu clicks
  setupMenuListeners();
});

// Check Admin Authentication
function checkAdminAuthentication() {
  const adminSession = JSON.parse(localStorage.getItem('adminSession'));
  
  if (!adminSession || !adminSession.isAdmin) {
    // Redirect to login if not authenticated
    alert('Access denied! Only admins can access this dashboard.');
    window.location.href = 'login.html';
    return;
  }
  
  // Display admin name
  const adminName = document.getElementById('adminName');
  if (adminName) {
    adminName.textContent = adminSession.firstName + ' ' + adminSession.lastName;
  }
}

// Load all data from localStorage
function loadAllData() {
  loadAccounts();
  loadProperties();
  loadInquiries();
  loadEmployees();
  updateStats();
}

// Load Accounts
function loadAccounts() {
  const accounts = JSON.parse(localStorage.getItem('registeredUsers')) || [];
  const tableBody = document.getElementById('accountsTableBody');
  
  if (accounts.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No accounts found. Accounts will appear here when users register.</td></tr>';
    return;
  }
  
  tableBody.innerHTML = '';
  accounts.forEach((account, index) => {
    const row = document.createElement('tr');
    const registrationDate = new Date(account.registrationDate || Date.now()).toLocaleDateString();
    
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${account.firstName}</td>
      <td>${account.lastName}</td>
      <td>${account.email}</td>
      <td>${account.phone}</td>
      <td>${registrationDate}</td>
      <td>
        <button class="btn-action btn-view" onclick="viewAccount(${index})">
          <i class="fa fa-eye"></i> View
        </button>
        <button class="btn-action btn-delete" onclick="deleteAccountConfirm(${index})">
          <i class="fa fa-trash"></i> Delete
        </button>
      </td>
    `;
    tableBody.appendChild(row);
  });
}

// View Account Details
function viewAccount(index) {
  const accounts = JSON.parse(localStorage.getItem('registeredUsers')) || [];
  const account = accounts[index];
  currentAccountId = index;
  
  if (!account) return;
  
  const detailsHtml = `
    <ul class="details-list">
      <li>
        <span class="details-label">First Name:</span>
        <span class="details-value">${account.firstName}</span>
      </li>
      <li>
        <span class="details-label">Last Name:</span>
        <span class="details-value">${account.lastName}</span>
      </li>
      <li>
        <span class="details-label">Email:</span>
        <span class="details-value">${account.email}</span>
      </li>
      <li>
        <span class="details-label">Phone:</span>
        <span class="details-value">${account.phone}</span>
      </li>
      <li>
        <span class="details-label">Registration Date:</span>
        <span class="details-value">${new Date(account.registrationDate || Date.now()).toLocaleDateString()}</span>
      </li>
    </ul>
  `;
  
  document.getElementById('accountDetailsBody').innerHTML = detailsHtml;
  $('#viewAccountModal').modal('show');
}

// Delete Account
function deleteAccountConfirm(index) {
  if (confirm('Are you sure you want to delete this account? This action cannot be undone.')) {
    deleteAccount(index);
  }
}

function deleteAccount(index = null) {
  if (index === null) {
    index = currentAccountId;
  }
  
  const accounts = JSON.parse(localStorage.getItem('registeredUsers')) || [];
  accounts.splice(index, 1);
  localStorage.setItem('registeredUsers', JSON.stringify(accounts));
  
  $('#viewAccountModal').modal('hide');
  loadAccounts();
  updateStats();
  showAlert('Account deleted successfully!', 'success');
}

// Load Properties
function loadProperties() {
  const properties = JSON.parse(localStorage.getItem('properties')) || [];
  const tableBody = document.getElementById('propertiesTableBody');
  
  if (properties.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No properties found. Properties will appear here when new listings are added.</td></tr>';
    return;
  }
  
  tableBody.innerHTML = '';
  properties.forEach((property, index) => {
    const statusText = property.propertyStatus === 'sale' ? 'For Sale' : 
                       (property.propertyStatus === 'rent' ? 'For Rent' : 'For Lease');
    const location = property.propertyCity || property.propertyAddress || 'N/A';
    const price = property.propertyPrice || 'N/A';
    const beds = property.propertyBedrooms || '-';
    const baths = property.propertyBathrooms || '-';
    const area = property.propertyArea || '-';
    
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${property.propertyTitle}</td>
      <td>${property.propertyType}</td>
      <td>${location}</td>
      <td>${property.propertyCurrency || 'LKR'} ${price}</td>
      <td>${beds}</td>
      <td>${baths}</td>
      <td>${area}</td>
      <td><span class="status-badge status-${property.propertyStatus}">${statusText}</span></td>
      <td>
        <button class="btn btn-sm btn-info" onclick="viewProperty(${index})" title="View Details">
          <i class="fa fa-eye"></i>
        </button>
        <button class="btn btn-sm btn-danger" onclick="deletePropertyConfirm(${index})" title="Delete">
          <i class="fa fa-trash"></i>
        </button>
      </td>
    `;
    tableBody.appendChild(row);
  });
}

// View Property Details
function viewProperty(index) {
  const properties = JSON.parse(localStorage.getItem('properties')) || [];
  const property = properties[index];
  currentPropertyId = index;
  
  if (!property) return;
  
  const detailsHtml = `
    <ul class="details-list">
      <li>
        <span class="details-label">Title:</span>
        <span class="details-value">${property.propertyTitle}</span>
      </li>
      <li>
        <span class="details-label">Type:</span>
        <span class="details-value">${property.propertyType}</span>
      </li>
      <li>
        <span class="details-label">Status:</span>
        <span class="details-value"><span class="status-badge status-${property.propertyStatus}">${property.propertyStatus === 'sale' ? 'For Sale' : (property.propertyStatus === 'rent' ? 'For Rent' : 'For Lease')}</span></span>
      </li>
      <li>
        <span class="details-label">Price:</span>
        <span class="details-value">${property.propertyCurrency || 'LKR'} ${property.propertyPrice || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Address:</span>
        <span class="details-value">${property.propertyAddress || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">City:</span>
        <span class="details-value">${property.propertyCity || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">State/Province:</span>
        <span class="details-value">${property.propertyState || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">ZIP Code:</span>
        <span class="details-value">${property.propertyZipCode || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Country:</span>
        <span class="details-value">${property.propertyCountry || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Area:</span>
        <span class="details-value">${property.propertyArea || 'N/A'} sq ft</span>
      </li>
      <li>
        <span class="details-label">Bedrooms:</span>
        <span class="details-value">${property.propertyBedrooms || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Bathrooms:</span>
        <span class="details-value">${property.propertyBathrooms || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Year Built:</span>
        <span class="details-value">${property.propertyYearBuilt || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Garage/Parking:</span>
        <span class="details-value">${property.propertyGarage || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Features:</span>
        <span class="details-value">${property.features && property.features.length > 0 ? property.features.join(', ') : 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Description:</span>
        <span class="details-value">${property.propertyDescription || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Owner Name:</span>
        <span class="details-value">${property.ownerName || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Owner Email:</span>
        <span class="details-value">${property.ownerEmail || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Owner Phone:</span>
        <span class="details-value">${property.ownerPhone || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Listed Date:</span>
        <span class="details-value">${new Date(property.listedDate || Date.now()).toLocaleDateString()}</span>
      </li>
    </ul>
  `;
  
  document.getElementById('propertyDetailsBody').innerHTML = detailsHtml;
  $('#viewPropertyModal').modal('show');
}

// Delete Property
function deletePropertyConfirm(index) {
  if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
    deleteProperty(index);
  }
}

function deleteProperty(index = null) {
  if (index === null) {
    index = currentPropertyId;
  }
  
  const properties = JSON.parse(localStorage.getItem('properties')) || [];
  properties.splice(index, 1);
  localStorage.setItem('properties', JSON.stringify(properties));
  
  $('#viewPropertyModal').modal('hide');
  loadProperties();
  updateStats();
  showAlert('Property deleted successfully!', 'success');
}

// Load Inquiries
function loadInquiries() {
  const inquiries = JSON.parse(localStorage.getItem('inquiries')) || [];
  const tableBody = document.getElementById('inquiriesTableBody');
  
  if (inquiries.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No inquiries found. Inquiries will appear here when customers contact you.</td></tr>';
    return;
  }
  
  tableBody.innerHTML = '';
  inquiries.forEach((inquiry, index) => {
    const row = document.createElement('tr');
    const inquiryDate = new Date(inquiry.inquiryDate || Date.now()).toLocaleDateString();
    
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${inquiry.contactName}</td>
      <td>${inquiry.contactEmail}</td>
      <td>${inquiry.contactPhone}</td>
      <td>${inquiry.contactSubject}</td>
      <td>${inquiry.contactPropertyType || 'N/A'}</td>
      <td>${inquiryDate}</td>
      <td>
        <button class="btn-action btn-view" onclick="viewInquiry(${index})">
          <i class="fa fa-eye"></i> View
        </button>
        <button class="btn-action btn-delete" onclick="deleteInquiryConfirm(${index})">
          <i class="fa fa-trash"></i> Delete
        </button>
      </td>
    `;
    tableBody.appendChild(row);
  });
}

// View Inquiry Details
function viewInquiry(index) {
  const inquiries = JSON.parse(localStorage.getItem('inquiries')) || [];
  const inquiry = inquiries[index];
  currentInquiryId = index;
  
  if (!inquiry) return;
  
  const detailsHtml = `
    <ul class="details-list">
      <li>
        <span class="details-label">Name:</span>
        <span class="details-value">${inquiry.contactName}</span>
      </li>
      <li>
        <span class="details-label">Email:</span>
        <span class="details-value">${inquiry.contactEmail}</span>
      </li>
      <li>
        <span class="details-label">Phone:</span>
        <span class="details-value">${inquiry.contactPhone}</span>
      </li>
      <li>
        <span class="details-label">Subject:</span>
        <span class="details-value">${inquiry.contactSubject}</span>
      </li>
      <li>
        <span class="details-label">Property Type:</span>
        <span class="details-value">${inquiry.contactPropertyType || 'N/A'}</span>
      </li>
      <li>
        <span class="details-label">Message:</span>
        <span class="details-value">${inquiry.contactMessageText}</span>
      </li>
      <li>
        <span class="details-label">Inquiry Date:</span>
        <span class="details-value">${new Date(inquiry.inquiryDate || Date.now()).toLocaleDateString()}</span>
      </li>
    </ul>
  `;
  
  document.getElementById('inquiryDetailsBody').innerHTML = detailsHtml;
  $('#viewInquiryModal').modal('show');
}

// Delete Inquiry
function deleteInquiryConfirm(index) {
  if (confirm('Are you sure you want to delete this inquiry? This action cannot be undone.')) {
    deleteInquiry(index);
  }
}

function deleteInquiry(index = null) {
  if (index === null) {
    index = currentInquiryId;
  }
  
  const inquiries = JSON.parse(localStorage.getItem('inquiries')) || [];
  inquiries.splice(index, 1);
  localStorage.setItem('inquiries', JSON.stringify(inquiries));
  
  $('#viewInquiryModal').modal('hide');
  loadInquiries();
  updateStats();
  showAlert('Inquiry deleted successfully!', 'success');
}

// Load Employees
function loadEmployees() {
  const employees = JSON.parse(localStorage.getItem('employees')) || [];
  const tableBody = document.getElementById('employeesTableBody');
  
  if (employees.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No employees found. Click "Add Employee" to add staff members.</td></tr>';
    return;
  }
  
  tableBody.innerHTML = '';
  employees.forEach((employee, index) => {
    const row = document.createElement('tr');
    const hireDate = new Date(employee.hireDate || Date.now()).toLocaleDateString();
    
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${employee.employeeId}</td>
      <td>${employee.firstName}</td>
      <td>${employee.lastName}</td>
      <td>${employee.email}</td>
      <td>${employee.phone}</td>
      <td>${employee.position}</td>
      <td>${hireDate}</td>
      <td>
        <button class="btn-action btn-edit" onclick="editEmployee(${index})">
          <i class="fa fa-edit"></i> Edit
        </button>
        <button class="btn-action btn-delete" onclick="deleteEmployeeConfirm(${index})">
          <i class="fa fa-trash"></i> Delete
        </button>
      </td>
    `;
    tableBody.appendChild(row);
  });
}

// Handle Add Employee
function handleAddEmployee(event) {
  event.preventDefault();
  
  const firstName = document.getElementById('empFirstName').value.trim();
  const lastName = document.getElementById('empLastName').value.trim();
  const email = document.getElementById('empEmail').value.trim();
  const phone = document.getElementById('empPhone').value.trim();
  const position = document.getElementById('empPosition').value.trim();
  const employeeId = document.getElementById('empEmployeeId').value.trim();
  
  if (!firstName || !lastName || !email || !phone || !position || !employeeId) {
    showEmployeeMessage('Please fill in all fields', 'danger');
    return;
  }
  
  // Check for duplicate Employee ID
  const employees = JSON.parse(localStorage.getItem('employees')) || [];
  if (employees.some(emp => emp.employeeId === employeeId)) {
    showEmployeeMessage('Employee ID already exists!', 'danger');
    return;
  }
  
  const newEmployee = {
    firstName,
    lastName,
    email,
    phone,
    position,
    employeeId,
    hireDate: new Date().toISOString()
  };
  
  employees.push(newEmployee);
  localStorage.setItem('employees', JSON.stringify(employees));
  
  // Clear form
  document.getElementById('addEmployeeForm').reset();
  showEmployeeMessage('Employee added successfully!', 'success');
  
  setTimeout(() => {
    $('#addEmployeeModal').modal('hide');
    loadEmployees();
    updateStats();
  }, 1500);
}

// Delete Employee
function deleteEmployeeConfirm(index) {
  if (confirm('Are you sure you want to delete this employee?')) {
    deleteEmployee(index);
  }
}

function deleteEmployee(index) {
  const employees = JSON.parse(localStorage.getItem('employees')) || [];
  employees.splice(index, 1);
  localStorage.setItem('employees', JSON.stringify(employees));
  
  loadEmployees();
  updateStats();
  showAlert('Employee deleted successfully!', 'success');
}

// Edit Employee (placeholder for future implementation)
function editEmployee(index) {
  alert('Edit functionality coming soon!');
}

// Update Statistics
function updateStats() {
  const accounts = JSON.parse(localStorage.getItem('registeredUsers')) || [];
  const properties = JSON.parse(localStorage.getItem('properties')) || [];
  const inquiries = JSON.parse(localStorage.getItem('inquiries')) || [];
  const employees = JSON.parse(localStorage.getItem('employees')) || [];
  
  document.getElementById('totalAccounts').textContent = accounts.length;
  document.getElementById('totalProperties').textContent = properties.length;
  document.getElementById('totalInquiries').textContent = inquiries.length;
  document.getElementById('totalEmployees').textContent = employees.length;
}

// Setup Menu Listeners
function setupMenuListeners() {
  const menuItems = document.querySelectorAll('.menu-item');
  
  menuItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Remove active class from all items
      menuItems.forEach(m => m.classList.remove('active'));
      
      // Add active class to clicked item
      this.classList.add('active');
      
      // Get section name
      const section = this.dataset.section;
      
      // Hide all sections
      const sections = document.querySelectorAll('.dashboard-section');
      sections.forEach(s => s.classList.remove('active'));
      
      // Show selected section
      const sectionId = `${section}-section`;
      const selectedSection = document.getElementById(sectionId);
      if (selectedSection) {
        selectedSection.classList.add('active');
      }
    });
  });
}

// Handle Change Password
function handleChangePassword(event) {
  event.preventDefault();
  
  const currentPassword = document.getElementById('currentPassword').value;
  const newPassword = document.getElementById('newPassword').value;
  const confirmNewPassword = document.getElementById('confirmNewPassword').value;
  
  const adminSession = JSON.parse(localStorage.getItem('adminSession'));
  
  if (!adminSession) {
    showPasswordMessage('Admin session not found', 'danger');
    return;
  }
  
  if (adminSession.password !== currentPassword) {
    showPasswordMessage('Current password is incorrect', 'danger');
    return;
  }
  
  if (newPassword !== confirmNewPassword) {
    showPasswordMessage('New passwords do not match', 'danger');
    return;
  }
  
  if (newPassword.length < 8) {
    showPasswordMessage('New password must be at least 8 characters', 'danger');
    return;
  }
  
  adminSession.password = newPassword;
  localStorage.setItem('adminSession', JSON.stringify(adminSession));
  
  showPasswordMessage('Password changed successfully!', 'success');
  
  setTimeout(() => {
    document.getElementById('changePasswordForm').reset();
    $('#changePasswordModal').modal('hide');
  }, 1500);
}

// Show Password Message
function showPasswordMessage(message, type) {
  const messageDiv = document.getElementById('passwordMessage');
  messageDiv.className = `alert alert-${type}`;
  messageDiv.textContent = message;
  messageDiv.style.display = 'block';
}

// Show Employee Message
function showEmployeeMessage(message, type) {
  const messageDiv = document.getElementById('employeeMessage');
  messageDiv.className = `alert alert-${type}`;
  messageDiv.textContent = message;
  messageDiv.style.display = 'block';
}

// Show Alert
function showAlert(message, type) {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type}`;
  alertDiv.innerHTML = `<i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
  alertDiv.style.cssText = 'position: fixed; top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
  
  document.body.appendChild(alertDiv);
  
  setTimeout(() => {
    alertDiv.remove();
  }, 3000);
}

// Logout Admin
function logoutAdmin() {
  if (confirm('Are you sure you want to logout?')) {
    localStorage.removeItem('adminSession');
    window.location.href = 'login.html';
  }
}

// Initialize Employee Modal on show
$('#addEmployeeModal').on('show.bs.modal', function() {
  document.getElementById('addEmployeeForm').reset();
  document.getElementById('employeeMessage').style.display = 'none';
});

// Initialize Password Modal on show
$('#changePasswordModal').on('show.bs.modal', function() {
  document.getElementById('changePasswordForm').reset();
  document.getElementById('passwordMessage').style.display = 'none';
});
