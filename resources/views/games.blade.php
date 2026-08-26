@extends('adminlte::page')

@section('title', 'Game Profile')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Game  List</h1>
        <!-- Tombol untuk membuka Modal New Game -->
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createGameModal">
            <i class="fas fa-plus"></i> New Game
        </button>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Row tempat card game akan dimuat secara dinamis -->
    <div class="row" id="games-container">
        <!-- Loading state -->
        <div class="col-12 text-center" id="loading-text">
            <p class="text-muted">Memuat data game dari API...</p>
        </div>
    </div>
</div>

<div class="modal fade" id="createGameModal" tabindex="-1" role="dialog" aria-labelledby="createGameModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createGameForm">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createGameModalLabel">Tambah Game Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="gameTitle">Title</label>
                        <input type="text" class="form-control" id="gameTitle" name="title" required placeholder="Masukkan judul game...">
                    </div>
                    <div class="form-group">
                        <label for="gameAuthor">Author</label>
                        <input type="text" class="form-control" id="gameAuthor" name="author" required placeholder="Masukkan nama author...">
                    </div>
                    <div class="form-group">
                        <label for="gameRelease">Release Year</label>
                        <input type="number" class="form-control" id="gameRelease" name="release" required placeholder="Contoh: 2026">
                    </div>
                    <div class="form-group">
                        <label for="gameDetail">Description / Detail</label>
                        <textarea class="form-control" id="gameDetail" name="detail" rows="3" placeholder="Masukkan deskripsi game..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSaveGame">Simpan Game</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL SUBMIT REVIEW ================= -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="reviewForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Submit Review untuk: <span id="modal-game-title" class="font-weight-bold"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">Silakan pilih rating (1 - 5):</p>
                    
                    <!-- Radio Button Rating 1 sampai 5 -->
                    <div class="d-flex justify-content-around align-items-center mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input review-radio" type="radio" name="review" id="rating{{ $i }}" value="{{ $i }}" style="transform: scale(1.5); cursor: pointer;">
                                <label class="form-check-label ml-2 font-weight-bold" for="rating{{ $i }}" style="cursor: pointer; font-size: 18px;">
                                    {{ $i }} 🌟
                                </label>
                            </div>
                        @endfor
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>
<!-- ================= END MODAL SUBMIT REVIEW ================= -->

<!-- ================= MODAL VIEW DETAIL ================= -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailModalLabel">Detail Game: <span id="detail-game-title" class="font-weight-bold"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <strong>Author:</strong> <span id="detail-game-author" class="float-right text-muted"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Release Year:</strong> <span id="detail-game-release" class="float-right text-muted"></span>
                    </li>
                </ul>
                <div class="form-group">
                    <label class="font-weight-bold">Description / Detail:</label>
                    <p id="detail-game-desc" class="p-3 bg-light rounded text-secondary mb-0" style="min-height: 80px;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ================= END MODAL VIEW DETAIL ================= -->
@stop

@section('css')
    <style>
        .game-card {
            transition: transform 0.2s;
        }
        .game-card:hover {
            transform: translateY(-5px);
        }
    </style>
@stop

@section('js')
<!-- Sertakan Axios CDN -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Ambil dan tampilkan data game dari API
        axios.get('http://localhost:8000/api/games')
            .then(response => {
                const container = document.getElementById('games-container');
                document.getElementById('loading-text').remove(); // Hapus teks loading

                const responseJson = response.data;
                const games = responseJson.data && responseJson.data.data ? responseJson.data.data : [];

                if (!games || games.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Tidak ada data game ditemukan.</p></div>';
                    return;
                }

                games.forEach(game => {
                    const initials = game.title ? game.title.substring(0, 2).toUpperCase() : 'GM';
                    
                    // Amankan perhitungan review agar tidak NaN
                    const totalReview = game.total_review || 0;
                    const totalScore = game.total_score || 0;
                    const averageRating = totalReview > 0 ? (totalScore / totalReview).toFixed(1) : '0.0';

                    const cardHtml = `
                        <div class="col-md-4 mb-4">
                            <div class="card card-primary card-outline game-card h-100">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <div style="width: 90px; height: 90px; line-height: 90px; background-color: #007bff; color: #fff; font-size: 28px; font-weight: bold; border-radius: 50%; margin: 0 auto 15px auto;">
                                            ${initials}
                                        </div>
                                    </div>

                                    <h3 class="profile-username text-center">${game.title || 'Tanpa Judul'}</h3> 

                                    <ul class="list-group list-group-unbordered mb-3"> 
                                        <li class="list-group-item">
                                            <b>Total Review</b> <a class="float-right text-dark">${totalReview} Review (${averageRating} 🌟)</a>
                                        </li>
                                    </ul>

                                    <table width="100%">
                                        <tr>
                                            <td style="padding-right: 5px;">
                                                <button type="button" class="btn btn-primary btn-block btn-sm open-review-modal" 
                                                    data-id="${game.id}" 
                                                    data-title="${game.title}">
                                                    <b>Submit Review</b>
                                                </button>
                                            </td>
                                            <td style="padding-left: 5px;">
                                                <button type="button" class="btn btn-secondary btn-block btn-sm open-detail-modal"
                                                    data-title="${game.title || 'Tanpa Judul'}"
                                                    data-author="${game.author || 'Unknown'}"
                                                    data-release="${game.release || '-'}"
                                                    data-detail="${game.detail || 'Tidak ada keterangan detail.'}">
                                                    <b>View Detail</b>
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;

                    container.insertAdjacentHTML('beforeend', cardHtml);
                });

                // Event listener untuk tombol Submit Review (Membuka Modal)
                document.querySelectorAll('.open-review-modal').forEach(button => {
                    button.addEventListener('click', function () {
                        const gameId = this.getAttribute('data-id');
                        const gameTitle = this.getAttribute('data-title');

                        document.getElementById('modal-game-title').innerText = gameTitle;
                        const form = document.getElementById('reviewForm');
                        form.setAttribute('data-game-id', gameId);

                        document.querySelectorAll('.review-radio').forEach(radio => radio.checked = false);
                        $('#reviewModal').modal('show');
                    });
                });

                // Event listener untuk tombol View Detail
                document.querySelectorAll('.open-detail-modal').forEach(button => {
                    button.addEventListener('click', function () {
                        document.getElementById('detail-game-title').innerText = this.getAttribute('data-title');
                        document.getElementById('detail-game-author').innerText = this.getAttribute('data-author');
                        document.getElementById('detail-game-release').innerText = this.getAttribute('data-release');
                        document.getElementById('detail-game-desc').innerText = this.getAttribute('data-detail');

                        $('#detailModal').modal('show');
                    });
                });
 
                // Event listener pilihan rating review
                document.querySelectorAll('.review-radio').forEach(radio => {
                    radio.addEventListener('change', function () {
                        const ratingValue = this.value;
                        const form = document.getElementById('reviewForm');
                        const gameId = form.getAttribute('data-game-id');

                        axios.post('http://localhost:8000/api/reviews', {
                            game_id: gameId,
                            score: ratingValue
                        }, {
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            $('#reviewModal').modal('hide');
                            alert('Review berhasil dikirim!');
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Gagal mengirim review:', error);
                            alert('Terjadi kesalahan saat mengirim review. Periksa console.');
                        });
                    });
                });

            })
            .catch(error => {
                console.error('Error fetching games with Axios:', error);
                const container = document.getElementById('games-container');
                if (container) {
                    container.innerHTML = `
                        <div class="col-12 text-center">
                            <p class="text-danger">Gagal memuat data dari API menggunakan Axios. Periksa console untuk detail.</p>
                        </div>
                    `;
                }
            });

        // 2. Event listener untuk Submit Form Create Game (Dipindah ke luar agar aman dan terikat sejak awal)
        const createForm = document.getElementById('createGameForm');
        if (createForm) {
            createForm.addEventListener('submit', function (e) {
                e.preventDefault(); // Mencegah reload halaman secara total

                const formData = {
                    title: document.getElementById('gameTitle').value,
                    author: document.getElementById('gameAuthor').value,
                    release: document.getElementById('gameRelease').value,
                    detail: document.getElementById('gameDetail').value,
                    total_review:0,
                    total_score:0

                };

                const btnSave = document.getElementById('btnSaveGame');
                btnSave.disabled = true;
                btnSave.innerText = 'Menyimpan...';

                axios.post('http://localhost:8000/api/games', formData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    $('#createGameModal').modal('hide');
                    alert('Game baru berhasil ditambahkan!');
                    location.reload(); // Refresh halaman untuk menampilkan data baru
                })
                .catch(error => {
                    console.error('Gagal menambahkan game:', error);
                    alert('Gagal menambahkan game. Pastikan semua field terisi dengan benar.');
                    btnSave.disabled = false;
                    btnSave.innerText = 'Simpan Game';
                });
            });
        }
    });
</script>
@stop