<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
      <h6 class="fw-semibold mb-0">Dashboard</h6>
      <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
          <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
            Dashboard
          </a>
        </li>
        <li>-</li>
        <li class="fw-medium">AI</li>
      </ul>
    </div>

    <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
      <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
          <div class="card-body p-20">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div>
                <p class="fw-medium text-primary-light mb-1">Total Users</p>
                <h6 class="mb-0">20,000</h6>
              </div>
              <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
              </div>
            </div>
            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
              <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> +5000</span>
              Last 30 days users
            </p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
          <div class="card-body p-20">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div>
                <p class="fw-medium text-primary-light mb-1">Total Subscription</p>
                <h6 class="mb-0">15,000</h6>
              </div>
              <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
              </div>
            </div>
            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
              <span class="d-inline-flex align-items-center gap-1 text-danger-main"><iconify-icon icon="bxs:down-arrow" class="text-xs"></iconify-icon> -800</span>
              Last 30 days subscription
            </p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
          <div class="card-body p-20">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div>
                <p class="fw-medium text-primary-light mb-1">Total Free Users</p>
                <h6 class="mb-0">5,000</h6>
              </div>
              <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
              </div>
            </div>
            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
              <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> +200</span>
              Last 30 days users
            </p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
          <div class="card-body p-20">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div>
                <p class="fw-medium text-primary-light mb-1">Total Income</p>
                <h6 class="mb-0">$42,000</h6>
              </div>
              <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
              </div>
            </div>
            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
              <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> +$20,000</span>
              Last 30 days income
            </p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
          <div class="card-body p-20">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div>
                <p class="fw-medium text-primary-light mb-1">Total Expense</p>
                <h6 class="mb-0">$30,000</h6>
              </div>
              <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl mb-0"></iconify-icon>
              </div>
            </div>
            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
              <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> +$5,000</span>
              Last 30 days expense
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Tables Section -->
    <div class="row gy-4 mt-1">
      <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
              <h6 class="text-lg mb-0">Sales Statistic</h6>
              <select class="form-select bg-base form-select-sm w-auto radius-8">
                <option>Yearly</option>
                <option>Monthly</option>
                <option>Weekly</option>
                <option>Today</option>
              </select>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-8">
              <h6 class="mb-0">$27,200</h6>
              <span class="text-sm fw-semibold rounded-pill bg-success-focus text-success-main border br-success px-8 py-4 line-height-1 d-flex align-items-center gap-1">
                10% <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
              </span>
              <span class="text-xs fw-medium">+ $1500 Per Day</span>
            </div>
            <div id="chart" class="pt-28 apexcharts-tooltip-style-1"></div>
          </div>
        </div>
      </div>
 
    </div>
</div>
