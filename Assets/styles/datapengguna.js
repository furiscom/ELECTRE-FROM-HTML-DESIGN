// Sample user data
let users = [
    { id: 1, email: 'admin@gmail.com', name: 'Admin', level: 'admin' },
    { id: 2, email: 'user@gmail.com', name: 'User', level: 'user' }
];

// Current page state
let currentPage = 1;
let entriesPerPage = 10;
let searchTerm = '';

// DOM Elements
const userTableBody = document.getElementById('userData');
const searchInput = document.getElementById('searchInput');
const entriesSelect = document.getElementById('entriesSelect');

// Initialize the table
function initializeTable() {
    displayUsers();
    setupEventListeners();
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    searchInput.addEventListener('input', (e) => {
        searchTerm = e.target.value.toLowerCase();
        currentPage = 1;
        displayUsers();
    });

    // Entries per page change
    entriesSelect.addEventListener('change', (e) => {
        entriesPerPage = parseInt(e.target.value);
        currentPage = 1;
        displayUsers();
    });

    // Add button functionality
    document.querySelector('.add-button').addEventListener('click', () => {
        showAddUserModal();
    });
}

// Display users in the table
function displayUsers() {
    // Filter users based on search term
    const filteredUsers = users.filter(user => 
        user.email.toLowerCase().includes(searchTerm) ||
        user.name.toLowerCase().includes(searchTerm) ||
        user.level.toLowerCase().includes(searchTerm)
    );

    // Calculate pagination
    const start = (currentPage - 1) * entriesPerPage;
    const end = start + entriesPerPage;
    const paginatedUsers = filteredUsers.slice(start, end);

    // Clear existing table content
    userTableBody.innerHTML = '';

    // Add users to table
    paginatedUsers.forEach((user, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>${user.email}</td>
            <td>${user.name}</td>
            <td>${user.level}</td>
            <td>
                <div class="action-buttons">
                    <button class="action-button view-button" onclick="viewUser(${user.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-button edit-button" onclick="editUser(${user.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-button delete-button" onclick="deleteUser(${user.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        userTableBody.appendChild(row);
    });

    updatePaginationInfo(filteredUsers.length);
}

// Update pagination information
function updatePaginationInfo(totalItems) {
    const start = (currentPage - 1) * entriesPerPage + 1;
    const end = Math.min(start + entriesPerPage - 1, totalItems);
    
    document.querySelector('.entries-info').textContent = 
        `Showing ${start} to ${end} of ${totalItems} entries`;

    // Update pagination buttons
    const totalPages = Math.ceil(totalItems / entriesPerPage);
    const pagination = document.querySelector('.pagination');
    pagination.innerHTML = `
        <button class="prev-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Previous</button>
        ${generatePageButtons(totalPages)}
        <button class="next-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>
    `;
}

// Generate pagination buttons
function generatePageButtons(totalPages) {
    let buttons = '';
    for (let i = 1; i <= totalPages; i++) {
        buttons += `
            <button class="page-btn ${currentPage === i ? 'active' : ''}" 
                    onclick="changePage(${i})">${i}</button>
        `;
    }
    return buttons;
}

// Change page
function changePage(page) {
    currentPage = page;
    displayUsers();
}

// CRUD Operations
function viewUser(id) {
    const user = users.find(u => u.id === id);
    alert(`User Details:\nEmail: ${user.email}\nName: ${user.name}\nLevel: ${user.level}`);
}

function editUser(id) {
    const user = users.find(u => u.id === id);
    // Implementation for edit functionality would go here
    alert(`Edit user: ${user.name}`);
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        users = users.filter(u => u.id !== id);
        displayUsers();
    }
}

// Initialize when document is loaded
document.addEventListener('DOMContentLoaded', initializeTable);

// Tambahkan kode ini di file script.js

// Modal elements
const modal = document.getElementById('addUserModal');
const addUserForm = document.getElementById('addUserForm');
const closeBtn = document.querySelector('.close');
const cancelBtn = document.querySelector('.cancel-btn');

// Fungsi untuk membuka modal
function showAddUserModal() {
    modal.style.display = 'block';
    addUserForm.reset(); // Reset form ketika dibuka
}

// Fungsi untuk menutup modal
function closeModal() {
    modal.style.display = 'none';
}

// Event listeners untuk modal
closeBtn.addEventListener('click', closeModal);
cancelBtn.addEventListener('click', closeModal);
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeModal();
    }
});

// Handle form submission
addUserForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Ambil nilai dari form
    const newUser = {
        id: users.length + 1,
        email: document.getElementById('email').value,
        name: document.getElementById('nama').value,
        level: document.getElementById('level').value
    };
    
    // Tambahkan user baru ke array users
    users.push(newUser);
    
    // Perbarui tampilan tabel
    displayUsers();
    
    // Tutup modal
    closeModal();
    
    // Tampilkan pesan sukses
    alert('Data pengguna berhasil ditambahkan!');
});