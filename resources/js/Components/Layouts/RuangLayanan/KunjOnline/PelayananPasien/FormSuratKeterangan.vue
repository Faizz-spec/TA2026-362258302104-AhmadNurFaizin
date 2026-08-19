<template>
    <div class="card m-4 p-3 shadow-sm border-0">
        <div class="card-header bg-primary text-white p-3 text-black d-flex justify-content-between align-items-center rounded">
        <h1 class="m-0 fs-4">Form Surat Keterangan</h1>
            <div class="d-flex gap-2">
                 <Link  class="btn bg-secondary text-white btn-sm">Kembali Ke Diagnosa</Link>
        <Link  class="btn bg-secondary text-white btn-sm">Kembali Ke Daftar Pasien</Link>
            </div>
        </div>
        <div class="card-body">
        <div class="row g-4">
          <h2 class="text-decoration-underline fs-6">Data Pasien</h2>
          <div class="col-12 col-md-6">
            <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">No MR</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" disabled />
              </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold  text-end">Nama</label>
              <div class="col-sm-8">
                <input class="form-control" rows="2" disabled></input>
              </div>
            </div>

            <div class="row mb-3 align-items-start">
            <label class="col-sm-4 col-form-label fw-bold text-end">Tempat/tgl Lahir</label>
            <div class="col-sm-8">
                <div class="row">
                <div class="col-md-6 mb-2 mb-md-0">
                    <input type="text" class="form-control" placeholder="Tempat Lahir">
                </div>
                <div class="col-md-6">
                    <input type="date" class="form-control">
                </div>
                </div>
            </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold  text-end">Agama</label>
              <div class="col-sm-8">
                <input class="form-control" rows="2" disabled></input>
              </div>
            </div>
          </div>

          <!-- KOLOM KANAN -->
          <div class="col-12 col-md-6">
            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold text-end">Pekerjaan</label>
              <div class="col-sm-8">
                <input class="form-control" disabled rows="2" ></input>
              </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold text-end">Alamat</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="2" disabled></textarea>
              </div>
            </div>
            <div class="row mb-3 align-items-start">
                <label class="col-sm-4 col-form-label fw-bold text-end">Desa - Kec</label>
                <div class="col-sm-8">
                    <div class="row">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <input type="text" class="form-control" placeholder="Tempat Lahir">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control">
                    </div>
                    </div>
                </div>
                </div>
          </div>

        </div>
         <div class="row g-4">
          <h2 class="text-decoration-underline fs-6">Data Surat</h2>
          <div class="col-12 col-md-6">
            <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">No MR</label>
              <div class="col-sm-8">
                <select name="" id="">
                    <option value="">SURAT KETERANGAN SEHAT </option>
                </select>
              </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold  text-end">No Surat</label>
              <div class="col-sm-8">
                <input class="form-control" rows="2" disabled></input>
              </div>
            </div>

            <div class="row mb-3 ">
               <label class="col-sm-4 col-form-label text-end fw-bold">Tanggal ijin</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control">
                </div>
                <label class="col-sm-2 col-form-label text-end fw-bold">s/d</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control">
                </div>
            </div>
            <div class="row mb-3 ">
               <label class="col-sm-4 col-form-label text-end fw-bold">Tanggal/Jam Kematian</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control">
                </div>
                <label class="col-sm-2 col-form-label text-end fw-bold">Jam</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control">
                </div>
            </div>
            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold  text-end">Keterangan Kematian</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="2"></textarea>
              </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold  text-end">Keperluan</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="2"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-4">
          <h2 class="text-decoration-underline fs-6">Data Pemeriksaan</h2>
          <div class="col-12 col-md-6">
            <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Tinggi </label>
              <div class="col-sm-8">
               <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">cm</span>
                </div>
              </div>
            </div>

            <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Berat </label>
              <div class="col-sm-8">
               <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">kg</span>
                </div>
              </div>
            </div>

           <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Respiratory Rate</label>
              <div class="col-sm-8">
               <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">/minute</span>
                </div>
              </div>
            </div>

           <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Heart Rate</label>
              <div class="col-sm-8">
               <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">bpm</span>
                </div>
              </div>
            </div>

              <div class="row mb-3 ">
               <label class="col-sm-4 col-form-label text-end fw-bold">Sistole</label>
                <div class="col-sm-4">
                <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">mmHg</span>
                </div>
                </div>
                <label class="col-sm-2 col-form-label text-end">Diastole</label>
                <div class="col-sm-2">
                <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">mmHg</span>
                </div>
                </div>
            </div>
              <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Suhu</label>
              <div class="col-sm-8">
               <div class="input-group">
                    <input type="number" class="form-control" disabled>
                    <span class="input-group-text">C</span>
                </div>
              </div>
            </div>
          </div>
          

          <!-- KOLOM KANAN -->
          <div class="col-12 col-md-6">
            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold text-end">Mata Ka/Ki</label>
              <div class="col-sm-8">
                <select class="form-control" name="" id="">
                    <option value="">Baik</option>
                    <option value="">Tidak Baik</option>
                </select>
              </div>
            </div>

              <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold text-end">Telinga Ka/Ki</label>
              <div class="col-sm-8">
                <select class="form-control" name="" id="">
                    <option value="">Baik</option>
                    <option value="">Tidak Baik</option>
                </select>
              </div>
            </div>
            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold text-end">Tes Buta Warna</label>
              <div class="col-sm-8">
                <select class="form-control" name="" id="">
                    <option value="">Normal</option>
                    <option value="">Partial</option>
                    <option value="">Total</option>
                </select>
              </div>
            </div>

            <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Keterangan Lain</label>
              <div class="col-sm-8">
                    <textarea type="number" class="form-control"> </textarea>
              </div>
            </div>
             <div class="row mb-3 ">
              <label class="col-sm-4 col-form-label fw-bold text-end">Hasil Pemeriksaan</label>
              <div class="col-sm-8">
                    <textarea type="number" class="form-control"> </textarea>
              </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label class="col-sm-4 col-form-label fw-bold text-end">Dokter Jaga</label>
              <div class="col-sm-8">
                <select class="form-control" name="" id="">
                    <option value="">Normal</option>
                    <option value="">Partial</option>
                    <option value="">Total</option>
                </select>
              </div>
            </div>

          </div>

        </div>

       
        </div>
    </div>
</template>