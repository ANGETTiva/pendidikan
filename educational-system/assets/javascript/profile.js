function editProfile() {
    alert('Fitur edit profil akan segera tersedia.');
}

function changePassword() {
    const newPass = prompt('Masukkan password baru:');
    if (newPass && newPass.length >= 6) {
        const confirmPass = prompt('Konfirmasi password baru:');
        if (newPass === confirmPass) {
            alert('Password berhasil diubah! (Simulasi)');
        } else {
            alert('Password tidak cocok!');
        }
    } else if (newPass) {
        alert('Password minimal 6 karakter!');
    }
}
