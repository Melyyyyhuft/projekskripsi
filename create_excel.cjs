const fs = require('fs');
const path = require('path');

// A very basic node script to write a simple CSV and rename it to xlsx?
// No, the user wants a real Excel file with columns.
// Instead of writing a complex buffer, I'll just use a small base64 string of an empty/dummy xlsx file.
// Wait, I can just use child_process to install xlsx locally.
const { execSync } = require('child_process');
console.log('Installing xlsx...');
execSync('npm install xlsx --no-save', { stdio: 'inherit' });

const XLSX = require('xlsx');
const data = [
    ['Tahun Ajaran', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar'],
    ['2024/2025', 'Berapakah 1+1?', '1', '2', '3', '4', 'B'],
    ['2024/2025', 'Siapakah penemu bola lampu pijar yang praktis?', 'Isaac Newton', 'Albert Einstein', 'Nikola Tesla', 'Thomas Edison', 'D'],
    ['2024/2025', 'Apa ibukota Indonesia?', 'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'A']
];
const ws = XLSX.utils.aoa_to_sheet(data);

// Auto-adjust column widths
ws['!cols'] = [
    { wch: 15 }, // Tahun Ajaran
    { wch: 45 }, // Pertanyaan
    { wch: 20 }, // Opsi A
    { wch: 20 }, // Opsi B
    { wch: 20 }, // Opsi C
    { wch: 20 }, // Opsi D
    { wch: 15 }  // Jawaban Benar
];

const wb = XLSX.utils.book_new();
XLSX.utils.book_append_sheet(wb, ws, 'Template Soal');
XLSX.writeFile(wb, path.join(__dirname, 'public', 'template_bank_soal.xlsx'));
console.log('Excel file created successfully!');
