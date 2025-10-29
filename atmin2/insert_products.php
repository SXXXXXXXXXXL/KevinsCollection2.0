<?php
// Logika PHP utama sekarang ada di ajax_insert_product.php
// File ini hanya untuk menampilkan form
?>
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Insert New Product</h2>
    
    <div id="feedback-message" class="hidden mb-4"></div>

    <form id="insert-product-form" action="ajax_insert_product.php" method="post" enctype="multipart/form-data" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div><label for="product_title" class="block text-sm font-medium text-gray-700">Product Title</label><input type="text" name="product_title" id="product_title" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required></div>
             <div><label for="product_keyword" class="block text-sm font-medium text-gray-700">Product Keywords</label><input type="text" name="product_keyword" id="product_keyword" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required></div>
        </div>
        <div><label for="product_desc" class="block text-sm font-medium text-gray-700">Product Description</label><textarea name="product_desc" id="product_desc" rows="3" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required></textarea></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="product_category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="product_category" id="product_category" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required>
                    <option value="">Select Category</option>
                    <?php $result_cat = mysqli_query($con, "SELECT * FROM categories"); while($row = mysqli_fetch_assoc($result_cat)){ echo "<option value='{$row['category_id']}'>".htmlspecialchars($row['category_title'])."</option>"; } ?>
                </select>
            </div>
            <div>
                <label for="product_brand" class="block text-sm font-medium text-gray-700">Brand</label>
                <select name="product_brand" id="product_brand" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required>
                    <option value="">Select Brand</option>
                    <?php $result_brand = mysqli_query($con, "SELECT * FROM brands"); while($row = mysqli_fetch_assoc($result_brand)){ if (!empty(trim($row['brand_title']))) { echo "<option value='{$row['brand_id']}'>".htmlspecialchars($row['brand_title'])."</option>"; }} ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div><label for="product_price" class="block text-sm font-medium text-gray-700">Product Price</label><input type="number" name="product_price" id="product_price" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required></div>
             <div><label for="is_recommended" class="block text-sm font-medium text-gray-700">Recommend?</label><select name="is_recommended" id="is_recommended" class="mt-1 p-2 block w-full border border-gray-300 rounded-md" required><option value="0" selected>No</option><option value="1">Yes</option></select></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Product Images (Max 3)</label>
            <div id="drop-zone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md transition-colors duration-300">
                <div class="space-y-1 text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                    <div class="flex text-sm text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                            <span>Upload files</span>
                            <input id="file-upload" name="file-upload" type="file" class="sr-only" multiple accept="image/*">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                </div>
            </div>
        </div>

        <div id="preview-container" class="mt-4 grid grid-cols-3 gap-4"></div>

        <div class="text-center pt-4">
            <button type="submit" id="submit-button" class="cursor-pointer py-2 px-8 bg-gray-800 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700">Insert Product</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileUpload = document.getElementById('file-upload');
    const previewContainer = document.getElementById('preview-container');
    const form = document.getElementById('insert-product-form');
    const submitButton = document.getElementById('submit-button');
    const feedbackMessage = document.getElementById('feedback-message');

    let fileList = [];
    const MAX_FILES = 3;

    // Fungsi untuk mencegah aksi default browser
    const preventDefaults = (e) => {
        e.preventDefault();
        e.stopPropagation();
    };

    // Fungsi untuk memberi highlight pada drop zone
    const highlight = () => dropZone.classList.add('border-blue-500', 'bg-blue-50');
    const unhighlight = () => dropZone.classList.remove('border-blue-500', 'bg-blue-50');

    // Event listeners untuk drop zone
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Menangani file yang di-drop
    dropZone.addEventListener('drop', (e) => {
        handleFiles(e.dataTransfer.files);
    });

    // Menangani file yang dipilih dari input
    fileUpload.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // Fungsi utama untuk memproses file
    const handleFiles = (files) => {
        for (const file of files) {
            if (fileList.length < MAX_FILES && file.type.startsWith('image/')) {
                fileList.push(file);
            }
        }
        updatePreviews();
    };

    // Fungsi untuk menampilkan pratinjau
    const updatePreviews = () => {
        previewContainer.innerHTML = '';
        fileList.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = () => {
                const previewElement = document.createElement('div');
                previewElement.className = 'relative';
                previewElement.innerHTML = `
                    <img src="${reader.result}" class="w-full h-32 object-cover rounded-md">
                    <button type="button" class="remove-btn absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" data-index="${index}">&times;</button>
                `;
                previewContainer.appendChild(previewElement);
            };
            reader.readAsDataURL(file);
        });
    };
    
    // Menangani penghapusan pratinjau
    previewContainer.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('remove-btn')) {
            const index = e.target.getAttribute('data-index');
            fileList.splice(index, 1);
            updatePreviews();
        }
    });

    // Menangani submit form
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inserting...';

        const formData = new FormData(form);
        
        // Hapus placeholder input file, lalu tambahkan file dari list kita
        formData.delete('file-upload');
        fileList.forEach((file, index) => {
            formData.append(`product_image${index + 1}`, file, file.name);
        });
        
        fetch('ajax_insert_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            feedbackMessage.innerHTML = `<div class='p-4 mb-4 text-sm rounded-lg ${data.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}'>${data.message}</div>`;
            feedbackMessage.classList.remove('hidden');
            if(data.success) {
                form.reset();
                fileList = [];
                updatePreviews();
            }
        })
        .catch(error => {
            feedbackMessage.innerHTML = `<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100'>An error occurred: ${error}</div>`;
            feedbackMessage.classList.remove('hidden');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Insert Product';
        });
    });
});
</script>