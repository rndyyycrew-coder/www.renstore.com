// Data konfigurasi paket top up untuk setiap game
const games = {
    ml: {
        name: 'Mobile Legends',
        userLabel: 'User ID',
        userPlaceholder: 'ID Mobile Legends',
        extraLabel: 'Nomor Server',
        extraName: 'server',
        extraPlaceholder: 'Nomor Server',
        packages: [
            '3 Diamond - Rp. 1.200',
            '5 Diamond - Rp. 1.500',
            '12 Diamond - Rp. 3.300',
            '20 Diamond - Rp. 5.000',
            '30 Diamond - Rp. 8.000',
            '44 Diamond - Rp. 12.000',
            '59 Diamond - Rp. 15.000',
            '85 Diamond - Rp. 22.000',
            '170 Diamond - Rp. 65.000',
            '240 Diamond - Rp. 68.000',
            '296 Diamond - Rp. 114.000'
        ]
    },
    ff: {
        name: 'Free Fire',
        userLabel: 'Username / User ID',
        userPlaceholder: 'ID Free Fire',
        extraLabel: null,
        extraName: null,
        extraPlaceholder: null,
        packages: [
            '5 Diamond - Rp. 1.500',
            '12 Diamond - Rp. 3.300',
            '50 Diamond - Rp. 11.000',
            '70 Diamond - Rp. 13.000',
            '140 Diamond - Rp. 25.000',
            '355 Diamond - Rp. 57.000',
            '720 Diamond - Rp. 135.000',
            '1450 Diamond - Rp. 270.000',
            '2180 Diamond - Rp. 400.000'
        ]
    },
    pubg: {
        name: 'PUBG Mobile',
        userLabel: 'Username / User ID',
        userPlaceholder: 'ID PUBG Mobile',
        extraLabel: null,
        extraName: null,
        extraPlaceholder: null,
        packages: [
            '60 UC - Rp. 15.000',
            '125 UC - Rp. 30.000',
            '325 UC - Rp. 75.000',
            '660 UC - Rp. 145.000',
            '1320 UC - Rp. 280.000'
        ]
    }
};

// Ambil parameter game dari URL, gunakan default ml jika tidak valid
function getGameKey() {
    const params = new URLSearchParams(window.location.search);
    const key = params.get('game')?.toLowerCase();
    return games[key] ? key : 'ml';
}

// Render form dinamis sesuai game yang dipilih
function renderForm(gameKey) {
    const game = games[gameKey];
    document.querySelector('.section-title').textContent = `Top Up ${game.name}`;
    document.getElementById('game').value = game.name;
    document.title = `Top Up ${game.name} - RENSTORE`;

    const dynamicFields = document.getElementById('dynamic-fields');
    dynamicFields.innerHTML = `
        <label>${game.userLabel}</label>
        <input type="text" name="user_id" placeholder="${game.userPlaceholder}" required>
        ${game.extraLabel ? `
            <label>${game.extraLabel}</label>
            <input type="text" name="${game.extraName}" placeholder="${game.extraPlaceholder}" required>
        ` : ''}
    `;

    const nominalOptions = document.getElementById('nominal-options');
    nominalOptions.innerHTML = game.packages.map(pkg => `<button type="button" class="nominal">${pkg}</button>`).join('');
    nominalOptions.querySelectorAll('.nominal').forEach(btn => {
        btn.addEventListener('click', () => pilih(btn.textContent, btn));
    });
}

// Pilih paket nominal dan simpan ke field tersembunyi
function pilih(n, btn) {
    document.getElementById('nominal').value = n;
    document.querySelectorAll('.nominal').forEach(el => el.classList.remove('selected'));
    btn.classList.add('selected');
}

// Validasi form sebelum submit
function validateForm() {
    const nominal = document.getElementById('nominal').value.trim();
    if (!nominal) {
        alert('Silakan pilih paket nominal terlebih dahulu.');
        return false;
    }
    return true;
}

// Jalankan renderForm saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
    renderForm(getGameKey());
});