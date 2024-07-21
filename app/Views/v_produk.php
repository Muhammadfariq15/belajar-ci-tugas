<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('failed')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php 
        $failed = session()->getFlashdata('failed');
        if (is_array($failed)) {
            foreach ($failed as $error) {
                echo $error . '<br>';
            }
        } else {
            echo $failed;
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    Tambah Data
</button>
<a type="button" class="btn btn-success" href="<?= base_url() ?>produk/download">
    Download Data
</a>
<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nama</th>
            <th scope="col">Harga</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Foto</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produk as $index => $produk) : ?>
            <tr>
                <th scope="row"><?php echo $index + 1 ?></th>
                <td><?php echo $produk['nama'] ?></td>
                <td><?php echo $produk['harga'] ?></td>
                <td><?php echo $produk['jumlah'] ?></td>
                <td>
                    <?php if ($produk['foto'] != '' and file_exists("img/" . $produk['foto'] . "")) : ?>
                        <img src="<?php echo base_url() . "img/" . $produk['foto'] ?>" width="100px">
                    <?php endif; ?>
                </td>
                <td>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal-<?= $produk['id'] ?>">Edit</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-<?= $produk['id'] ?>">Delete</button>
                </td>
            </tr>
            <!-- Edit Modal Begin -->
            <!-- Your Edit Modal Code Here -->
            <!-- Edit Modal End -->
        <?php endforeach ?>
    </tbody>
</table>

<!-- Add Modal Begin -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('produk') ?>" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Harga</label>
                        <input type="text" name="harga" class="form-control" id="harga" placeholder="Harga Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Jumlah</label>
                        <input type="text" name="jumlah" class="form-control" id="jumlah" placeholder="Jumlah Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add Modal End -->

<?= $this->endSection() ?>

<script>
function validateForm() {
    var nama = document.getElementById("nama").value;
    var harga = document.getElementById("harga").value;
    var jumlah = document.getElementById("jumlah").value;
    
    if (nama.length < 5) {
        alert("Nama harus terdiri dari minimal 5 karakter.");
        return false;
    }
    if (isNaN(harga)) {
        alert("Harga harus berupa angka.");
        return false;
    }
    if (isNaN(jumlah)) {
        alert("Jumlah harus berupa angka.");
        return false;
    }
    return true;
}
</script>
