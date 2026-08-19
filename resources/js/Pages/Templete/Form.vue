<template>
  <AppLayout title="Form Components">
    <div class="container py-4">
      <div class="row mb-4">
        <div class="col">
          <h1 class="fw-bold">Form Components</h1>
          <p class="text-muted">Form elements and validation examples</p>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">Basic Form</h5>
          <form>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" placeholder="name@example.com" />
              <div class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" />
            </div>
            <div class="mb-3">
              <label for="textarea" class="form-label">Example textarea</label>
              <textarea class="form-control" id="textarea" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="select" class="form-label">Example select</label>
              <select class="form-select" id="select">
                <option selected>Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Checkboxes</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkbox1" />
                <label class="form-check-label" for="checkbox1">Default checkbox</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkbox2" checked />
                <label class="form-check-label" for="checkbox2">Checked checkbox</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Radios</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadio" id="radio1" checked />
                <label class="form-check-label" for="radio1">Default radio</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadio" id="radio2" />
                <label class="form-check-label" for="radio2">Second radio</label>
              </div>
            </div>
            <div class="mb-3">
              <label for="formFile" class="form-label">File input</label>
              <input class="form-control" type="file" id="formFile" />
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">Form Validation</h5>
          <form class="needs-validation" novalidate>
            <div class="mb-3">
              <label for="validationName" class="form-label">Name</label>
              <input type="text" class="form-control" id="validationName" required />
              <div class="invalid-feedback">Please provide a valid name.</div>
            </div>
            <div class="mb-3">
              <label for="validationEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="validationEmail" required />
              <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
            <div class="mb-3">
              <label for="validationPassword" class="form-label">Password</label>
              <input
                type="password"
                class="form-control"
                id="validationPassword"
                required
                minlength="8"
              />
              <div class="invalid-feedback">Password must be at least 8 characters.</div>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="validationTerms" required />
                <label class="form-check-label" for="validationTerms"
                  >Agree to terms and conditions</label
                >
                <div class="invalid-feedback">You must agree before submitting.</div>
              </div>
            </div>
            <button class="btn btn-primary" type="submit">Submit form</button>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Components/Layouts/AppLayouts.vue';
</script>

<style scoped>
  .form-control:invalid,
  .form-select:invalid {
    border-color: #dc3545;
  }
</style>
