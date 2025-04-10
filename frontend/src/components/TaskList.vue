<template>
  <div class="cms-container">
    <!-- Sidebar -->
    <div class="sidebar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
      <div class="sidebar-header">
        <h3 class="mb-0">Dashboard</h3>
        <button class="btn btn-sm btn-link d-md-none" @click="toggleSidebar">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="sidebar-menu">
        <div class="nav flex-column">
          <a href="#" class="nav-link active">
            <i class="bi bi-list-check me-2"></i> Tugas
          </a>
          <a href="#" class="nav-link">
            <i class="bi bi-people me-2"></i> Pegawai
          </a>
          <a href="#" class="nav-link">
            <i class="bi bi-bar-chart me-2"></i> Laporan
          </a>
          <a href="#" class="nav-link">
            <i class="bi bi-gear me-2"></i> Pengaturan
          </a>
        </div>
        
        <div class="sidebar-section mt-3">
          <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted" style="color: #fff !important;">Laporan</h6>
          <div class="nav flex-column">
            <a href="#" class="nav-link">
              <i class="bi bi-file-earmark-text me-2"></i> Bulanan
            </a>
            <a href="#" class="nav-link">
              <i class="bi bi-file-earmark-bar-graph me-2"></i> Kinerja
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
          <button class="btn btn-sm sidebar-toggle" @click="toggleSidebar">
            <i class="bi bi-list fs-5"></i>
          </button>
          
          <div class="d-flex align-items-center ms-auto">
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle me-2" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  3
                </span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                <li><a class="dropdown-item" href="#">Tugas baru ditambahkan</a></li>
                <li><a class="dropdown-item" href="#">Pengingat: Laporan bulanan</a></li>
                <li><a class="dropdown-item" href="#">Update sistem tersedia</a></li>
              </ul>
            </div>
            
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <!-- <div class="avatar me-2">
                  <img src="https://via.placeholder.com/32" alt="User" class="rounded-circle" width="32" height="32">
                </div> -->
                <span>Admin</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="page-content p-4">
        <div class="container-fluid py-4">
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h1 class="h3 mb-0">Daftar Tugas Pegawai</h1>
              <nav aria-label="breadcrumb" style="margin-top: 10px;">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a style="color: #000; text-decoration: none;" href="#">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Tugas</li>
                </ol>
              </nav>
            </div>
            <div class="d-flex gap-2">
              <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                  <i class="bi bi-search"></i>
                </span>
                <input 
                  type="text" 
                  class="form-control border-start-0" 
                  placeholder="Cari Berdasarkan Nama Pegawai" 
                  v-model="searchQuery"
                  @input="handleSearch"
                >
              </div>
              <b-button variant="outline-secondary" @click="fetchTasks" size="sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
              </b-button>
              <b-button variant="success" @click="openAddForm" size="sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Tugas
              </b-button>
            </div>
          </div>

          <!-- Loading spinner -->
          <div v-if="loading" class="text-center py-5">
            <b-spinner variant="success" label="Loading..."></b-spinner>
          </div>

          <!-- Table -->
          <div class="card">
            <div class="card-body p-0">
              <b-table
                v-if="!loading"
                striped
                hover
                responsive
                :items="filteredTasks"
                :fields="fields"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                class="mb-0"
                empty-text="Belum ada tugas yang tersedia"
                show-empty
              >
                <!-- Custom formatting for date -->
                <template #cell(date)="data">
                  {{ formatDate(data.value) }}
                </template>

                <!-- Custom formatting for currency -->
                <template #cell(hourly_rate)="data">
                  {{ formatCurrency(data.value) }}
                </template>
                
                <template #cell(additional_charges)="data">
                  <div v-if="data.item.is_multiple_employees">
                    <div v-for="(emp, index) in data.item.employee_contributions" :key="index">
                      {{ formatCurrency(emp.additional_charges) }}
                    </div>
                  </div>
                  <div v-else>
                    {{ formatCurrency(data.value || 0) }}
                  </div>
                </template>
                
                <template #cell(total_remuneration)="data">
                  {{ formatCurrency(data.value) }}
                </template>

                <!-- Actions column -->
                <template #cell(actions)="data">
                  <div class="d-flex justify-content-end gap-1">
                    <b-button variant="outline-info" size="sm" @click="showDetails(data.item)" title="Lihat Detail">
                      <i class="bi bi-info-circle"></i>
                    </b-button>
                    <b-button variant="outline-primary" size="sm" @click="edit(data.item)" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </b-button>
                    <b-button variant="outline-danger" size="sm" @click="confirmDelete(data.item)" title="Delete">
                      <i class="bi bi-trash"></i>
                    </b-button>
                  </div>
                </template>

                <!-- Empty state -->
                <template #empty>
                  <div class="text-center py-4">
                    <i class="bi bi-clipboard-check fs-1 text-secondary"></i>
                    <p class="mt-2 mb-3">Belum ada tugas yang tersedia</p>
                    <b-button variant="success" size="sm" @click="openAddForm">
                      <i class="bi bi-plus-lg me-1"></i> Tambah Tugas
                    </b-button>
                  </div>
                </template>

                <!-- Custom formatting for employee name -->
                <template #cell(employee_name)="data">
                  <div v-if="data.item.is_multiple_employees">
                    <div v-for="(emp, index) in data.item.employee_contributions" :key="index">
                      {{ emp.name }}
                    </div>
                  </div>
                  <div v-else>
                    {{ data.item.employee_name }}
                  </div>
                </template>

                <template #cell(employee_remuneration)="data">
                  <div v-if="data.item.is_multiple_employees">
                    <div v-for="(emp, index) in data.item.employee_contributions" :key="index">
                      {{ formatCurrency(emp.remuneration) }}
                    </div>
                  </div>
                  <div v-else>
                    {{ formatCurrency(data.item.total_remuneration) }}
                  </div>
                </template>

                <template #cell(hours_spent)="data">
                  <div v-if="data.item.is_multiple_employees">
                    <div v-for="(emp, index) in data.item.employee_contributions" :key="index">
                      {{ emp.hours }} jam
                    </div>
                  </div>
                  <div v-else>
                    {{ data.value }} jam
                  </div>
                </template>
              </b-table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <b-modal
      v-model="showForm"
      :title="form.id ? 'Edit Tugas' : 'Tambah Tugas'"
      header-bg-variant="primary"
      @hidden="resetForm"
      hide-footer
      size="lg"
    >
      <b-form @submit.prevent="submit">
        <b-form-group>
          <b-form-checkbox
            v-model="form.is_multiple_employees"
            @change="handleMultipleEmployeesChange"
          >
            Tugas dikerjakan oleh multiple pegawai
          </b-form-checkbox>
        </b-form-group>

        <template v-if="!form.is_multiple_employees">
          <b-form-group label="Nama Pegawai" label-for="employee_name">
            <b-form-input
              id="employee_name"
              v-model="form.employee_name"
              placeholder="Masukkan nama pegawai"
              required
            ></b-form-input>
          </b-form-group>

          <b-form-group label="Jam Kerja" label-for="hours_spent">
            <b-form-input
              id="hours_spent"
              type="number"
              v-model="form.hours_spent"
              placeholder="0"
              min="0"
              step="0.5"
              required
            ></b-form-input>
          </b-form-group>
        </template>

        <template v-else>
          <b-form-group label="Kontribusi Pegawai">
            <div class="mb-2">
              <b-row>
                <b-col cols="5">
                  <small class="text-muted">Nama Pegawai</small>
                </b-col>
                <b-col cols="4">
                  <small class="text-muted">Jam Kerja</small>
                </b-col>
                <!-- <b-col cols="3">
                  <small class="text-muted">Aksi</small>
                </b-col> -->
              </b-row>
            </div>
            <div v-for="(emp, index) in form.employee_contributions" :key="index" class="mb-3">
              <b-row>
                <b-col cols="5">
                  <b-form-input
                    v-model="emp.name"
                    placeholder="Nama pegawai"
                    required
                  ></b-form-input>
                </b-col>
                <b-col cols="4">
                  <b-form-input
                    v-model="emp.hours"
                    type="number"
                    placeholder="Jam kerja"
                    min="0"
                    step="0.5"
                    required
                  ></b-form-input>
                </b-col>
                <b-col cols="3">
                  <b-button variant="danger" @click="removeEmployee(index)" v-if="form.employee_contributions.length > 1">
                    <i class="bi bi-trash"></i>
                  </b-button>
                </b-col>
              </b-row>
            </div>
            <b-button variant="outline-primary" @click="addEmployee" size="sm">
              <i class="bi bi-plus-lg"></i> Tambah Pegawai
            </b-button>
          </b-form-group>
        </template>

        <b-form-group label="Deskripsi Tugas" label-for="task_description">
          <b-form-input
            id="task_description"
            v-model="form.task_description"
            placeholder="Masukkan deskripsi tugas"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group label="Tanggal" label-for="date">
          <b-form-input
            id="date"
            type="date"
            v-model="form.date"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group label="Tarif per Jam" label-for="hourly_rate">
          <b-form-input
            id="hourly_rate"
            type="number"
            v-model="form.hourly_rate"
            placeholder="0"
            min="0"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group label="Biaya Tambahan" label-for="additional_charges">
          <b-form-input
            id="additional_charges"
            type="number"
            v-model="form.additional_charges"
            placeholder="0"
            min="0"
          ></b-form-input>
        </b-form-group>

        <div class="d-flex justify-content-end gap-2 mt-3">
          <b-button variant="secondary" @click="showForm = false">
            Batal
          </b-button>
          <b-button type="submit" variant="success">
            Simpan
          </b-button>
        </div>
      </b-form>
    </b-modal>

    <!-- Delete Confirmation Modal -->
    <b-modal
      v-model="showDeleteConfirm"
      title="Konfirmasi Hapus"
      header-bg-variant="danger"
      header-text-variant="white"
      ok-variant="danger"
      ok-title="Hapus"
      cancel-title="Batal"
      @ok="executeDelete"
    >
      <p class="my-2">
        Apakah Anda yakin ingin menghapus tugas <b>{{ taskToDelete?.task_description }}</b> untuk pegawai <b>{{ taskToDelete?.employee_name }}</b>?
      </p>
    </b-modal>

    <!-- Detail Modal -->
    <b-modal
      v-model="showDetailModal"
      title="Detail Perhitungan"
      size="lg"
      hide-footer
      header-bg-variant="info"
      header-border-variant="info"
    >
      <template #modal-title>
        <i class="bi bi-calculator me-2"></i>Detail Perhitungan
      </template>
      
      <div v-if="selectedTask" class="detail-container">
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
              <i class="bi bi-info-circle me-2"></i>Informasi Tugas
            </h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-borderless m-0">
                <tbody>
                  <tr>
                    <th class="text-muted" width="30%">Nama Pegawai</th>
                    <td>{{ selectedTask.employee_name }}</td>
                  </tr>
                  <tr>
                    <th class="text-muted">Deskripsi</th>
                    <td>{{ selectedTask.task_description }}</td>
                  </tr>
                  <tr>
                    <th class="text-muted">Tanggal</th>
                    <td>{{ formatDate(selectedTask.date) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
              <i class="bi bi-currency-dollar me-2"></i>Komponen Biaya
            </h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-borderless m-0">
                <tbody>
                  <tr>
                    <th class="text-muted" width="30%">Jam Kerja</th>
                    <td>
                      <span class="badge bg-info">{{ selectedTask.hours_spent }} jam</span>
                    </td>
                  </tr>
                  <tr>
                    <th class="text-muted">Tarif per Jam</th>
                    <td>{{ formatCurrency(selectedTask.hourly_rate) }}</td>
                  </tr>
                  <tr v-if="calculationDetails">
                    <th class="text-muted">Biaya Dasar</th>
                    <td>{{ formatCurrency(calculationDetails.base_cost) }}</td>
                  </tr>
                  <tr>
                    <th class="text-muted">Biaya Tambahan</th>
                    <td>
                      <div v-if="selectedTask.is_multiple_employees">
                        <div class="d-flex align-items-center">
                          <span class="badge bg-warning me-2">{{ formatCurrency(calculationDetails?.additional_remuneration || 0) }}</span>
                          <small class="text-muted">(Bagian dari total {{ formatCurrency(calculationDetails?.additional_charges || 0) }})</small>
                        </div>
                      </div>
                      <div v-else>
                        <span class="badge bg-warning">{{ formatCurrency(calculationDetails?.additional_charges || 0) }}</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="calculationDetails && selectedTask.is_multiple_employees">
                    <th class="text-muted">Persentase Kontribusi</th>
                    <td>
                      <div class="progress" style="height: 20px; width: 200px">
                        <div 
                          class="progress-bar bg-success" 
                          role="progressbar" 
                          :style="{ width: calculationDetails.employee_percentage + '%' }"
                        >
                          {{ calculationDetails.employee_percentage.toFixed(1) }}%
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
              <i class="bi bi-cash-stack me-2"></i>Hasil Perhitungan
            </h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-borderless m-0">
                <tbody>
                  <tr v-if="calculationDetails && selectedTask.is_multiple_employees">
                    <th class="text-muted" width="30%">Remunerasi Dasar</th>
                    <td>{{ formatCurrency(calculationDetails.base_remuneration) }}</td>
                  </tr>
                  <tr v-if="calculationDetails && selectedTask.is_multiple_employees">
                    <th class="text-muted">Remunerasi Tambahan</th>
                    <td>{{ formatCurrency(calculationDetails.additional_remuneration) }}</td>
                  </tr>
                  <tr>
                    <th class="text-muted">Total Remunerasi</th>
                    <td>
                      <h4 class="text-success mb-0">
                        {{ formatCurrency(selectedTask.total_remuneration) }}
                      </h4>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="text-end mt-4">
          <b-button variant="secondary" @click="showDetailModal = false">
            <i class="bi bi-x-lg me-2"></i>Tutup
          </b-button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

// Sidebar state
const sidebarCollapsed = ref(window.innerWidth < 768);

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Handle window resize
window.addEventListener('resize', () => {
  if (window.innerWidth < 768) {
    sidebarCollapsed.value = true;
  }
});

// Define table fields
const fields = [
  { 
    key: 'employee_name', 
    label: 'Nama Pegawai',
    sortable: true
  },
  { 
    key: 'task_description', 
    label: 'Deskripsi',
    sortable: true
  },
  { 
    key: 'date', 
    label: 'Tanggal',
    sortable: true
  },
  { 
    key: 'hours_spent', 
    label: 'Total Jam',
    sortable: true
  },
  { 
    key: 'hourly_rate', 
    label: 'Tarif',
    sortable: true
  },
  { 
    key: 'additional_charges', 
    label: 'Tambahan',
    sortable: true
  },
  { 
    key: 'total_remuneration', 
    label: 'Total',
    sortable: true
  },
  { 
    key: 'actions', 
    label: 'Aksi',
    sortable: false
  }
];

const tasks = ref([]);
const loading = ref(false);
const showForm = ref(false);
const showDeleteConfirm = ref(false);
const taskToDelete = ref(null);

const form = ref({
  id: null,
  employee_name: '',
  task_description: '',
  date: '',
  hours_spent: 0,
  hourly_rate: 0,
  additional_charges: 0,
  is_multiple_employees: false,
  employee_contributions: []
});

const showDetailModal = ref(false);
const selectedTask = ref(null);
const calculationDetails = ref(null);

const searchQuery = ref('');
const filteredTasks = ref([]);

// Add sorting state
const sortBy = ref('');
const sortDesc = ref(false);

const handleSearch = () => {
  if (!searchQuery.value) {
    filteredTasks.value = [...tasks.value];
    return;
  }

  const query = searchQuery.value.toLowerCase();
  filteredTasks.value = tasks.value.filter(task => {
    return (
      task.employee_name?.toLowerCase().includes(query)
    );
  });
};

const fetchTasks = async () => {
  loading.value = true;
  try {
    const res = await api.get('/tasks');
    tasks.value = res.data;
    filteredTasks.value = res.data;
  } catch (error) {
    console.error(error);
    showToast('Gagal memuat data tugas', 'danger');
  } finally {
    loading.value = false;
  }
};

const submit = async () => {
  loading.value = true;
  try {
    if (form.value.id) {
      await api.put(`/tasks/${form.value.id}`, form.value);
      showToast('Tugas berhasil diperbarui', 'success');
    } else {
      await api.post('/tasks', form.value);
      showToast('Tugas berhasil ditambahkan', 'success');
    }
    resetForm();
    showForm.value = false;
    fetchTasks();
  } catch (error) {
    console.error(error);
    showToast('Gagal menyimpan data tugas', 'danger');
  } finally {
    loading.value = false;
  }
};

const edit = (task) => {
  form.value = { ...task };
  showForm.value = true;
};

const confirmDelete = (task) => {
  taskToDelete.value = task;
  showDeleteConfirm.value = true;
};

const executeDelete = async () => {
  if (!taskToDelete.value) return;
  
  loading.value = true;
  try {
    await api.delete(`/tasks/${taskToDelete.value.id}`);
    showToast('Tugas berhasil dihapus', 'success');
    taskToDelete.value = null;
    fetchTasks();
  } catch (error) {
    console.error(error);
    showToast('Gagal menghapus tugas', 'danger');
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  form.value = {
    id: null,
    employee_name: '',
    task_description: '',
    date: '',
    hours_spent: 0,
    hourly_rate: 0,
    additional_charges: 0,
    is_multiple_employees: false,
    employee_contributions: []
  };
};

const openAddForm = () => {
  resetForm();
  showForm.value = true;
};

const showToast = (message, variant = 'primary') => {
  // Using Bootstrap Vue toast
  if (window.$bvToast) {
    window.$bvToast.toast(message, {
      title: variant === 'danger' ? 'Error' : 'Notification',
      variant: variant,
      solid: true,
      autoHideDelay: 3000
    });
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric' 
  }).format(date);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount);
};

const handleMultipleEmployeesChange = (checked) => {
  if (checked) {
    form.value.employee_contributions = [{
      name: '',
      hours: 0
    }];
  } else {
    form.value.employee_contributions = [];
  }
};

const addEmployee = () => {
  form.value.employee_contributions.push({
    name: '',
    hours: 0
  });
};

const removeEmployee = (index) => {
  form.value.employee_contributions.splice(index, 1);
};

const showDetails = (task) => {
  selectedTask.value = task;
  try {
    const details = JSON.parse(task.calculation_details);
    calculationDetails.value = {
      ...details,
      additional_charges: task.additional_charges || 0,
      base_cost: details.base_cost,
      total_cost: details.total_cost,
      employee_percentage: details.employee_percentage,
      base_remuneration: details.base_remuneration,
      additional_remuneration: details.additional_remuneration,
      total_remuneration: task.total_remuneration
    };
    console.log('Calculation Details:', calculationDetails.value); // Untuk debugging
  } catch (e) {
    console.error('Error parsing calculation details:', e);
    calculationDetails.value = {
      base_cost: task.hourly_rate * task.hours_spent,
      additional_charges: task.additional_charges || 0,
      total_cost: task.total_remuneration,
    };
  }
  showDetailModal.value = true;
};

onMounted(fetchTasks);
</script>

<style>
/* CMS Layout Styles */
.cms-container {
  display: flex;
  min-height: 100vh;
}

/* Modal Header Styles */
.modal-header {
  color: white !important;
}

.modal-header .modal-title {
  color: white !important;
}

/* Sidebar Styles */
.sidebar {
  width: 250px;
  background-color: #343a40;
  color: #fff;
  transition: all 0.3s;
  z-index: 1000;
  flex-shrink: 0;
}

.sidebar-collapsed {
  width: 0;
  overflow: hidden;
}

.sidebar-header {
  padding: 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-menu {
  padding: 1rem 0;
}

.sidebar .nav-link {
  color: rgba(255, 255, 255, 0.75);
  padding: 0.5rem 1rem;
  display: flex;
  align-items: center;
  transition: all 0.2s;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
  color: #fff;
  background-color: rgba(255, 255, 255, 0.1);
}

.sidebar-heading {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Main Content Styles */
.main-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  background-color: #f8f9fa;
  transition: all 0.3s;
}

.navbar {
  padding: 0.5rem 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.sidebar-toggle {
  padding: 0.25rem 0.5rem;
  margin-right: 1rem;
}

.page-content {
  flex-grow: 1;
  overflow-y: auto;
}

.avatar {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
  .sidebar {
    position: fixed;
    height: 100%;
  }
  
  .sidebar-collapsed {
    transform: translateX(-100%);
  }
}

/* Card styling */
.card {
  border-radius: 0.5rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  margin-bottom: 1.5rem;
}

/* Table styling */
.table {
  margin-bottom: 0;
}

/* Dropdown styling */
.dropdown-menu {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  border: none;
  padding: 0.5rem 0;
}

.dropdown-item {
  padding: 0.5rem 1rem;
}

.dropdown-item:active {
  background-color: #198754;
}

/* Badge positioning */
.dropdown-toggle .badge {
  font-size: 0.6rem;
  margin-left: -0.5rem;
}

.table th {
  background-color: #f8f9fa;
}

.table-success th,
.table-success td {
  background-color: #d1e7dd !important;
}

.detail-container .card {
  transition: all 0.3s ease;
}

.detail-container .card:hover {
  transform: translateY(-2px);
}

.detail-container .card-header {
  padding: 1rem;
}

.detail-container .card-header h6 {
  color: #2c3e50;
  font-weight: 600;
}

.detail-container .table th {
  font-weight: 500;
}

.detail-container .progress {
  border-radius: 10px;
  background-color: #e9ecef;
}

.detail-container .progress-bar {
  border-radius: 10px;
  transition: width 0.6s ease;
}

/* Animasi untuk modal */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

/* Styling untuk tabel utama */
.table {
  margin-bottom: 0;
  vertical-align: middle;
}

.table th {
  font-weight: 500;
  color: #6c757d;
  background-color: #f8f9fa;
  border-bottom-width: 1px;
}

.table td {
  padding: 1rem;
  vertical-align: middle;
}

.btn-group-sm > .btn, .btn-sm {
  padding: 0.4rem 0.8rem;
  font-size: 0.875rem;
  border-radius: 0.4rem;
}

.badge {
  padding: 0.5rem 0.8rem;
  font-weight: 500;
}

/* Card styling */
.card {
  border-radius: 0.75rem;
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
  background-color: transparent;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

/* Button styling */
.btn {
  font-weight: 500;
  letter-spacing: 0.3px;
}

.btn-outline-info:hover,
.btn-outline-primary:hover,
.btn-outline-danger:hover {
  color: #fff;
}

/* Modal styling */
.modal-content {
  border: none;
  border-radius: 0.75rem;
}

.modal-header {
  border-top-left-radius: 0.75rem;
  border-top-right-radius: 0.75rem;
}

.modal-title {
  font-weight: 600;
  color: #2c3e50;
}
</style>