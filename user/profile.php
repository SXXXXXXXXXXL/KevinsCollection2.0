<?php
session_start();

if(!isset($_SESSION['username'])){
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'You have to login first'
    ];
    header("Location: ../index.php");
    exit();
}

include('../include/connect.php');
include('../functions/common_function.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo $_SESSION['username'] ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div id="app" class="flex-grow">
        <!-- Navbar -->
        <nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <a href="../index.php" class="flex items-center space-x-2">
                            <i class="fas fa-store text-2xl text-blue-500"></i>
                            <span class="text-white font-bold text-lg">Kevin's Collection</span>
                        </a>
                        <div class="hidden sm:flex sm:ml-6 space-x-1">
                            <a href="../index.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Home</a>
                            <a href="../display_all.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Products</a>
                            <div class="relative" @mouseenter="contactsOpen = true" @mouseleave="contactsOpen = false">
                                <button class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Contacts</button>
                                <div v-show="contactsOpen" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <a href="mailto:hildanekevin16@gmail.com" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-md"><i class="far fa-envelope mr-2"></i>E-mail</a>
                                    <a href="https://wa.me/6281290206155" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-md"><i class="fab fa-whatsapp mr-2"></i>Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <a href="../cart.php" class="text-gray-300 hover:text-white px-3 py-2 relative group">
                            <i class="fa-solid fa-cart-shopping text-xl group-hover:text-blue-500 transition-colors duration-200"></i>
                            <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"><?php cart_item() ?></span>
                            <span class="text-gray-300 text-sm hidden md:inline-block ml-2">Rp.<?php total_cart_price(); ?></span>
                        </a>
                        <a href="logout.php" class="bg-red-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-red-600 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <?php cart(); ?>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-12 gap-6">
                <!-- Sidebar -->
                <div class="col-span-12 md:col-span-3">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-900 text-white p-4">
                            <h2 class="text-xl font-semibold">Profile</h2>
                        </div>
                        <?php
                        $username = $_SESSION['username'];
                        $user_image_query = "SELECT * FROM user_table WHERE username='$username'";
                        $user_image_result = mysqli_query($con, $user_image_query);
                        $row_image = mysqli_fetch_array($user_image_result);
                        $user_image = $row_image['user_image'];
                        ?>
                        <div class="p-4">
                            <div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-4">
                                <img src="user_images/<?php echo $user_image ?>" class="w-full h-full object-cover" alt="Profile Image">
                            </div>
                            <h3 class="text-lg font-semibold text-center text-gray-900 mb-4"><?php echo $username ?></h3>
                            <nav class="space-y-2">
                                <a href="profile.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-200">
                                    <i class="fas fa-clock mr-2"></i>Pending Orders
                                </a>
                                <a href="profile.php?edit_account" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-200">
                                    <i class="fas fa-user-edit mr-2"></i>Edit Account
                                </a>
                                <a href="profile.php?my_orders" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-200">
                                    <i class="fas fa-shopping-bag mr-2"></i>My Orders
                                </a>
                                <a href="profile.php?delete_account" class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-md transition-colors duration-200">
                                    <i class="fas fa-user-times mr-2"></i>Delete Account
                                </a>
                                <a href="logout.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-span-12 md:col-span-9">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <?php 
                        get_user_order_details(); 
                        if(isset($_GET['edit_account'])){
                            include('edit_account.php');
                        }
                        if(isset($_GET['my_orders'])){
                            include('user_orders.php');
                        }
                        if(isset($_GET['delete_account'])){
                            include('delete_account.php');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include('../include/footer.php') ?>
    </div>

    <script>
        const { createApp } = Vue
        createApp({
            data() {
                return {
                    mobileMenu: false,
                    contactsOpen: false
                }
            }
        }).mount('#app')

        const trackingModal = document.getElementById('trackingModal');
    const detailsContainer = document.getElementById('trackingDetailsContainer');
    const historyContainer = document.getElementById('trackingHistoryContainer');

    async function showTrackingModal(courier, awb) {
        console.log(`Mulai showTrackingModal untuk Kurir: ${courier}, Resi: ${awb}`);

        // 1. Tampilkan modal dan status loading
        trackingModal.classList.remove('hidden');
        detailsContainer.innerHTML = '';
        historyContainer.innerHTML = `<p class="text-center text-gray-500 py-4">Mencari data tracking...</p>`;

        try {
            // 2. Panggil handler API di server Anda
            console.log("Melakukan fetch ke server...");
            const response = await fetch(`../api/track_handler.php?courier=${courier}&awb=${awb}`);
            console.log("Fetch selesai, status:", response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // 3. Coba parsing JSON
            console.log("Mencoba parsing response sebagai JSON...");
            const result = await response.json();
            console.log("Data JSON berhasil di-parse:", result);

            // 4. Cek status dari API BinderByte
            if (result.status !== 200) {
                historyContainer.innerHTML = `<p class="text-center text-red-500 py-4">${result.message}</p>`;
                return;
            }

            // 5. Render data ke modal
            console.log("Merender data ke modal...");
            const summary = result.data.summary;
            const detail = result.data.detail;
            const history = result.data.history;

            detailsContainer.innerHTML = `
                <div>
                    <p class="font-semibold text-gray-800">Ringkasan</p>
                    <p><strong>Kurir:</strong> ${summary.courier} (${summary.service})</p>
                    <p><strong>No. Resi:</strong> <span class="font-mono bg-gray-100 px-1 rounded">${summary.awb}</span></p>
                    <p><strong>Status:</strong> <span class="font-bold text-green-700">${summary.status}</span></p>
                    <p><strong>Update Terakhir:</strong> ${summary.date}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Pengiriman</p>
                    <p><strong>Pengirim:</strong> ${detail.shipper}</p>
                    <p><strong>Asal:</strong> ${detail.origin}</p>
                    <p><strong>Penerima:</strong> ${detail.receiver}</p>
                    <p><strong>Tujuan:</strong> ${detail.destination || '-'}</p>
                </div>
            `;

            if (history && history.length > 0) {
                 historyContainer.innerHTML = history.map(item => `
                    <div class="relative pl-8 pb-4 border-l border-gray-200 last:border-l-transparent last:pb-0">
                        <div class="absolute -left-2 top-0 h-4 w-4 rounded-full bg-blue-100 border-4 border-white flex items-center justify-center">
                             <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">${item.desc}</p>
                        <p class="text-xs text-gray-500">${item.date}</p>
                        ${item.location ? `<p class="text-xs text-gray-500">Lokasi: ${item.location}</p>` : ''}
                    </div>
                `).join('');
            } else {
                historyContainer.innerHTML = `<p class="text-center text-gray-500 py-4">Riwayat perjalanan tidak tersedia.</p>`;
            }
            console.log("Modal selesai dirender.");

        } catch (error) {
            console.error('Fetch error:', error);
            historyContainer.innerHTML = `<p class="text-center text-red-500 py-4">Terjadi kesalahan saat memuat data. Periksa tab Console untuk detail.</p>`;
        }
    }

    function closeTrackingModal() {
        trackingModal.classList.add('hidden');
    }
    </script>
</body>
</html>
