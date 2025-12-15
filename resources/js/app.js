import './bootstrap';

import Chart from 'chart.js/auto';
window.Chart = Chart;

import Shepherd from 'shepherd.js';
window.Shepherd = Shepherd;

import Masonry from 'masonry-layout';
import imagesLoaded from 'imagesloaded';

window.Masonry = Masonry;
window.imagesLoaded = imagesLoaded;

import Sortable from 'sortablejs';
window.Sortable = Sortable;

/**
 * Fungsi untuk menginisialisasi grafik pendaftaran (garis).
 * Fungsi ini akan dipanggil dari view Blade.
 */
window.initRegistrationChart = function () {
    const canvas = document.getElementById('registrationChart');
    if (!canvas) return; // Hentikan jika canvas tidak ditemukan

    // Ambil data dari atribut data-* dan ubah dari string JSON menjadi array JS
    const labels = JSON.parse(canvas.dataset.labels);
    const data = JSON.parse(canvas.dataset.data);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendaftaran Baru',
                data: data,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
};

/**
 * Fungsi untuk menginisialisasi grafik event terpopuler (batang).
 * Fungsi ini akan dipanggil dari view Blade.
 */
window.initPopularEventsChart = function () {
    const canvas = document.getElementById('popularEventsChart');
    if (!canvas) return; // Hentikan jika canvas tidak ditemukan

    // Ambil data dari atribut data-* dan ubah dari string JSON menjadi array JS
    const labels = JSON.parse(canvas.dataset.labels);
    const data = JSON.parse(canvas.dataset.data);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: data,
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
};