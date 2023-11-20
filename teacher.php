<?php

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Periksa peran pengguna
$role = $_SESSION['role'];

if ($role !== 'admin' && $role !== 'teacher') {
    // Jika peran tidak valid, keluar dan tampilkan pesan kesalahan
    echo "Anda tidak memiliki izin untuk mengakses halaman ini";
    exit();
}

// Koneksi ke database
require_once 'config.php';

// Ambil data siswa dari database
$query = "SELECT * FROM students";
$result = $conn->query($query);

// Inisialisasi variabel
$studentNama = $studentUH = $studentUTS = $studentUAS = $studentKalkulasi = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil ID siswa yang dipilih dari formulir
    $selectedStudentId = $_POST['student'];

    // Ambil detail siswa berdasarkan ID
    $queryDetail = "SELECT * FROM students WHERE id = $selectedStudentId";
    $resultDetail = $conn->query($queryDetail);

    // Tampilkan detail siswa
    if ($resultDetail->num_rows > 0) {
        $student = $resultDetail->fetch_assoc();

        // kalkulasi nilai siswa
        $kalkulasi = $student['uh'] * 0.10 + $student['uts'] * 0.30 + $student['uas'] * 0.60;
        $queryUpdate = "UPDATE students SET kalkulasi = $kalkulasi WHERE id = $selectedStudentId";
        $conn->query($queryUpdate);

        // Set data sesi
        $studentId = $student['id'];
        $studentNama = $student['nama'];
        $studentUH = $student['uh'];
        $studentUTS = $student['uts'];
        $studentUAS = $student['uas'];
        $studentKalkulasi = $student['kalkulasi'];
    } else {
        echo "Siswa tidak ditemukan.";
    }
}
?>

<?php




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo ucfirst($role); ?>Page - sekolah
    </title>
    <link href="output.css" rel="stylesheet">
</head>

<body>

    <nav class="flex items-center justify-between p-3 px-10 shadow-lg" aria-label="Global">
        <div class="flex lg:flex-1">
            <h1 class="text-xl lg:text-4xl text-gray-800 font-semibold uppercase mb-2">
                <span class="text-blue-600">
                    <?php echo ucfirst($role); ?>
                </span> Dashboard
            </h1>
        </div>
        <div class="flex lg:flex-1 lg:justify-end">
            <a href="index.php"
                class="text-sm font-semibold leading-6"><span
                    aria-hidden="true">&larr;</span> Log Out</a>
        </div>
    </nav>

    <div class="p-10">
        <div class="flex flex-col mb-10 w-fit">
            <h1 class="text-4xl text-gray-800 font-semibold capitalize mb-3">
                Selamat datang,
                <?php echo $_SESSION['username']; ?>!
            </h1>
            <span class="bg-blue-600 h-1 w-full rounded-full mb-2"></span>
        </div>

        <div class="shadow-lg rounded-lg p-6 flex flex-col justify-center items-center">
            <h2 class="text-3xl text-gray-800 text-center font-semibold uppercase mb-10">Daftar Siswa</h2>
                <form class="w-full mb-10" action="" method="post">
                    <div class="flex justify-start items-center py-3">
                        <div class="bg-gray-200 px-3 py-2 rounded-l-lg">
                            <label for="student">Pilih Siswa:</label>
                            <select class="bg-gray-200" name="student" id="student">
                                <?php
                            // Loop melalui hasil query dan tampilkan sebagai opsi dropdown
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>{$row['nama']}</option>";
                            }
                            ?>
                            </select>
                        </div>
                        <button class="bg-gray-300 rounded-r-lg px-3 py-2" onclick="detail()"
                            type="submit">Tampilkan
                            Detail</button>
                    </div>
                </form>

            <div class="w-full flex flex-col justify-center items-center">
                <div class="w-full grid grid-cols-2 gap-4">
                    <div class="col-span-2 flex justify-center items-center py-3 bg-gray-300 rounded-lg">
                        <h4 class="text-xl lg:text-2xl font-semibold">
                            <?php echo $studentNama ?>
                        </h4>
                    </div>
                    <div
                        class="col-span-2 lg:col-span-1 flex justify-around bg-gray-800 rounded-lg shadow-lg p-3 py-12 text-white">
                        <div class="text-center">
                            <h4 class="text-xl lg:text-2xl font-semibold">Nilai UH</h4>
                            <p class="text-xl lg:text-2xl">
                                <?php echo $studentUH; ?>
                            </p>
                        </div>
                        <div class="text-center">
                            <h4 class="text-xl lg:text-2xl font-semibold">Nilai UTS</h4>
                            <p class="text-xl lg:text-2xl">
                                <?php echo $studentUTS; ?>
                            </p>
                        </div>
                        <div class="text-center">
                            <h4 class="text-xl lg:text-2xl font-semibold">Nilai UAS</h4>
                            <p class="text-xl lg:text-2xl">
                                <?php echo $studentUAS; ?>
                            </p>
                        </div>
                    </div>
                    <div
                        class="col-span-2 lg:col-span-1 bg-blue-600 rounded-lg shadow-lg p-3 py-12 text-center text-white">
                        <h4 class="text-xl lg:text-2xl font-semibold">Kalkulasi Nilai</h4>
                        <p class="text-xl lg:text-2xl">
                            <?php echo $studentKalkulasi; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <button class="fixed bottom-0 right-0 m-10 rounded-full w-16 h-16 bg-gray-300 p-4" id="btn-print"><svg
                class="fill-gray-300 stroke-blue-400 stroke-2 w-full h-full active:animate-ping" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </button>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="script.js"></script>
</body>

</html>