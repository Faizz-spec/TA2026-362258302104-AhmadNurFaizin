<template>
  <div>
    <!-- Header -->
    <div class="az-header">
      <div class="container">
        <div class="az-header-left">
          <a href="#" class="az-logo"><span></span> azia</a>
        </div>
        <div class="az-header-menu">
          <ul class="nav">
            <li class="nav-item active show">
              <a href="#" class="nav-link"
                ><i class="typcn typcn-chart-area-outline"></i> Dashboard</a
              >
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link with-sub"><i class="typcn typcn-document"></i> Pages</a>
              <nav class="az-menu-sub">
                <a href="#" class="nav-link">Sign In</a>
                <a href="#" class="nav-link">Sign Up</a>
              </nav>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link"><i class="typcn typcn-chart-bar-outline"></i> Charts</a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link"><i class="typcn typcn-chart-bar-outline"></i> Forms</a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link with-sub"><i class="typcn typcn-book"></i> Components</a>
              <div class="az-menu-sub">
                <div class="container">
                  <div>
                    <nav class="nav">
                      <a href="#" class="nav-link">Buttons</a>
                      <a href="#" class="nav-link">Dropdown</a>
                      <a href="#" class="nav-link">Icons</a>
                      <a href="#" class="nav-link">Table</a>
                    </nav>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="az-header-right">
          <a href="#" class="az-header-search-link"><i class="far fa-file-alt"></i></a>
          <a href="#" class="az-header-search-link"><i class="fas fa-search"></i></a>
          <div class="az-header-message">
            <a href="#"><i class="typcn typcn-messages"></i></a>
          </div>
          <div class="dropdown az-header-notification">
            <a href="#" class="new"><i class="typcn typcn-bell"></i></a>
            <!-- Notification dropdown omitted for brevity -->
          </div>
          <div class="dropdown az-profile-menu">
            <a href="#" class="az-img-user"
              ><img src="https://www.bootstrapdash.com/demo/azia-free/img/faces/face1.jpg" alt=""
            /></a>
            <!-- Profile dropdown omitted for brevity -->
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="az-content az-content-dashboard">
      <div class="container">
        <div class="az-content-body">
          <div class="az-dashboard-one-title">
            <div>
              <h2 class="az-dashboard-title">Hi, welcome back!</h2>
              <p class="az-dashboard-text">Your web analytics dashboard template.</p>
            </div>
            <div class="az-content-header-right">
              <div class="media">
                <div class="media-body">
                  <label>Start Date</label>
                  <h6>Oct 10, 2018</h6>
                </div>
              </div>
              <div class="media">
                <div class="media-body">
                  <label>End Date</label>
                  <h6>Oct 23, 2018</h6>
                </div>
              </div>
              <div class="media">
                <div class="media-body">
                  <label>Event Category</label>
                  <h6>All Categories</h6>
                </div>
              </div>
              <a href="#" class="btn btn-purple">Export</a>
            </div>
          </div>

          <div class="az-dashboard-nav">
            <nav class="nav">
              <a class="nav-link active" data-toggle="tab" href="#">Overview</a>
              <a class="nav-link" data-toggle="tab" href="#">Audiences</a>
              <a class="nav-link" data-toggle="tab" href="#">Demographics</a>
              <a class="nav-link" data-toggle="tab" href="#">More</a>
            </nav>
            <nav class="nav">
              <a class="nav-link" href="#"><i class="far fa-save"></i> Save Report</a>
              <a class="nav-link" href="#"><i class="far fa-file-pdf"></i> Export to PDF</a>
              <a class="nav-link" href="#"><i class="far fa-envelope"></i>Send to Email</a>
              <a class="nav-link" href="#"><i class="fas fa-ellipsis-h"></i></a>
            </nav>
          </div>

          <!-- Dashboard Cards (dummy content) -->
          <div class="row row-sm mg-b-20">
            <div class="col-lg-7 ht-lg-100p">
              <div class="card card-dashboard-one">
                <div class="card-header">
                  <div>
                    <h6 class="card-title">Website Audience Metrics</h6>
                    <p class="card-text">
                      Audience to which the users belonged while on the current date range.
                    </p>
                  </div>
                  <div class="btn-group">
                    <button class="btn active">Day</button>
                    <button class="btn">Week</button>
                    <button class="btn">Month</button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="card-body-top">
                    <div>
                      <label class="mg-b-0">Users</label>
                      <h2>13,956</h2>
                    </div>
                    <div>
                      <label class="mg-b-0">Bounce Rate</label>
                      <h2>33.50%</h2>
                    </div>
                    <div>
                      <label class="mg-b-0">Page Views</label>
                      <h2>83,123</h2>
                    </div>
                    <div>
                      <label class="mg-b-0">Sessions</label>
                      <h2>16,869</h2>
                    </div>
                  </div>
                  <div class="flot-chart-wrapper">
                    <div id="flotChart" class="flot-chart"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 mg-t-20 mg-lg-t-0">
              <div class="row row-sm">
                <div class="col-sm-6">
                  <div class="card card-dashboard-two">
                    <div class="card-header">
                      <h6>
                        33.50% <i class="icon ion-md-trending-up tx-success"></i>
                        <small>18.02%</small>
                      </h6>
                      <p>Bounce Rate</p>
                    </div>
                    <div class="card-body">
                      <div class="chart-wrapper">
                        <div id="flotChart1" class="flot-chart"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mg-t-20 mg-sm-t-0">
                  <div class="card card-dashboard-two">
                    <div class="card-header">
                      <h6>
                        86k <i class="icon ion-md-trending-down tx-danger"></i> <small>0.86%</small>
                      </h6>
                      <p>Total Users</p>
                    </div>
                    <div class="card-body">
                      <div class="chart-wrapper">
                        <div id="flotChart2" class="flot-chart"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 mg-t-20">
                  <div class="card card-dashboard-three">
                    <div class="card-header">
                      <p>All Sessions</p>
                      <h6>
                        16,869
                        <small class="tx-success"><i class="icon ion-md-arrow-up"></i> 2.87%</small>
                      </h6>
                      <small
                        >The total number of sessions within the date range. It is the period time a
                        user is actively engaged with your website, page or app, etc.</small
                      >
                    </div>
                    <div class="card-body">
                      <div class="chart"><canvas id="chartBar5"></canvas></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- More Cards & Table (dummy content) -->
          <!-- ...copy the rest of the HTML structure as needed... -->
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="az-footer ht-40">
      <div class="container ht-100p pd-t-0-f">
        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block"
          >Copyright © bootstrapdash.com 2020</span
        >
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
          Free
          <a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank"
            >Bootstrap admin templates</a
          >
          from Bootstrapdash.com
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
  // Jika ingin chart interaktif, tambahkan script JS chart di sini
</script>

<!-- Import CSS Azia dan Bootstrap di resources/js/app.js -->
