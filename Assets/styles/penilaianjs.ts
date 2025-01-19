// Data penilaian
const penilaianData = [
    { no: 1, kode: 'A01', nama: 'Mahasiswa 1', c01: 3, c02: 2, c03: 5, c04: 5, c05: 2, c06: 1 },
    { no: 2, kode: 'A02', nama: 'Mahasiswa 2', c01: 5, c02: 3, c03: 4, c04: 5, c05: 2, c06: 2 },
    { no: 3, kode: 'A03', nama: 'Mahasiswa 3', c01: 4, c02: 5, c03: 3, c04: 1, c05: 2, c06: 4 },
    { no: 4, kode: 'A04', nama: 'Mahasiswa 4', c01: 2, c02: 2, c03: 5, c04: 4, c05: 5, c06: 1 },
    { no: 5, kode: 'A05', nama: 'Mahasiswa 5', c01: 3, c02: 2, c03: 2, c04: 3, c05: 1, c06: 2 },
    { no: 6, kode: 'A06', nama: 'Mahasiswa 6', c01: 2, c02: 4, c03: 5, c04: 4, c05: 1, c06: 1 },
    { no: 7, kode: 'A07', nama: 'Mahasiswa 7', c01: 5, c02: 2, c03: 4, c04: 1, c05: 5, c06: 2 },
    { no: 8, kode: 'A08', nama: 'Mahasiswa 8', c01: 5, c02: 4, c03: 1, c04: 2, c05: 4, c06: 2 },
    { no: 9, kode: 'A09', nama: 'Mahasiswa 9', c01: 2, c02: 5, c03: 2, c04: 3, c05: 4, c06: 4 }
];

// Fungsi untuk mengisi tabel
function populateTable(data) {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.no}</td>
            <td>${item.kode}</td>
            <td>${item.nama}</td>
            <td>${item.c01}</td>
            <td>${item.c02}</td>
            <td>${item.c03}</td>
            <td>${item.c04}</td>
            <td>${item.c05}</td>
            <td>${item.c06}</td>
            <td>
                <button class="btn-edit" onclick="editData(${item.no})">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Fungsi untuk pencarian
function handleSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.toLowerCase();

    const filteredData = penilaianData.filter(item => 
        item.kode.toLowerCase().includes(searchTerm) ||
        item.nama.toLowerCase().includes(searchTerm)
    );

    populateTable(filteredData);
}

// Fungsi untuk mengubah jumlah entries yang ditampilkan
function handleEntriesChange() {
    const select = document.getElementById('entriesSelect');
    const numEntries = parseInt(select.value);
    const displayedData = penilaianData.slice(0, numEntries);
    populateTable(displayedData);
}

// Fungsi untuk mengedit data (placeholder)
function editData(no) {
    console.log(`Editing data for row ${no}`);
    // Implementasi edit sesuai kebutuhan
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', handleSearch);
document.getElementById('entriesSelect').addEventListener('change', handleEntriesChange);

// Inisialisasi tabel
document.addEventListener('DOMContentLoaded', () => {
    populateTable(penilaianData);
});