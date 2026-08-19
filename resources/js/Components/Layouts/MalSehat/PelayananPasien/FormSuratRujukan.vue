<template>
    <div class="card m-4 p-3 shadow-sm border-0">
        <div class="card-header bg-primary text-white p-3 text-black d-flex justify-content-between align-items-center rounded">
        <h1 class="m-0 fs-4">Daftar Surat Rujukan</h1>
        <Link  class="btn bg-secondary text-white btn-sm">Kembali Ke Diagnosa</Link>
        </div>
        <div class="card-body">
            <button class="btn btn-primary"> <i class="bi bi-plus"></i> Tambah</button>
              <div class="table-responsive mt-4">
          <table class="table table-bordered table-sm text-center">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Id Rujukan</th>
                <th>Tanggal</th>
                <th>Surat Keterangan</th>
                <th>No Surat</th>
                <th>Rumah Sakit</th>
                <th>Poli</th>
                <th>Tenaga Medis</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
        </div>
    </div>
</template>