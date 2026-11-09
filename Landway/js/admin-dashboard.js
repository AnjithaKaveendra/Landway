// Compatibility entry point; the dashboard uses admin-dashboard-db.js.
const dashboardScript = document.createElement('script');
dashboardScript.src = 'js/admin-dashboard-db.js';
document.head.appendChild(dashboardScript);
