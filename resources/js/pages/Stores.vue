<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { UserStore } from "../stores/Users";
const {
  dialogVisible,
  editMode,
  dialogDelete,
  dialogDeleteSelect,
  loadingSubmitUser,
  loadingUser,
  users,
  id,
  name,
  email,
  password,
  profile_photo_path,
  phone_number,
  src,
  store_name,
  category_id,
  description,
  location,
  location_desc,
  search,
  per_page,
  sortField,
  sortOrder,
  selectedUsers,
  ids,
  filter_type,
  current_page,
  loadList,
  categoryList,
  file,
  storeIdFile,
  loadImport,
  role_id,
  roleList,
} = storeToRefs(UserStore());
const {
  showUsers,
  AddUser,
  opendeletemodel,
  openaddmodel,
  openEditModel,
  UpdateUser,
  deleteUser,
  onFileSelect,
  opendeleteSelect,
  deleteSelected,
  getSelect,
  importExcel,
} = UserStore();
onMounted(() => {
  showUsers(1, "store");
  getSelect();
});
const expandedRows = ref({});

// =================== items Skelton ===================
const usersload = ref(new Array(4));

// =================== Paginater ===================
const onPageChange = (event) => {
  const newPage = event.page + 1; // page يبدأ من 0
  per_page.value = event.rows;

  showUsers(newPage, "store");
};

// =================== Sorting ===================
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  showUsers(1, "store");
};

// =================== Searching ===================
let searchTimer = null;

watch(search, (value) => {
  // إلغاء أي مؤقت سابق
  clearTimeout(searchTimer);

  // إنشاء مؤقت جديد
  searchTimer = setTimeout(() => {
    showUsers(1, "store");
  }, 1000); // ← التأخير 1000ms || 1s
});

const importCsv = (event, storeId) => {
  file.value = event.files[0];
  storeIdFile.value = storeId;
};
</script>

<template>
  <AppLayout>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button
            v-if="$can('اضافة مستخدم')"
            label="جديد"
            icon="pi pi-plus"
            severity="secondary"
            class="ml-2"
            @click="openaddmodel"
          />

          <Button
            label="حذف"
            icon="pi pi-trash"
            severity="secondary"
            @click="opendeleteSelect"
            :disabled="!selectedUsers || !selectedUsers.length"
          />
        </template>

        <template #end>
          <Button
            label="تصدير"
            icon="pi pi-upload"
            severity="secondary"
            @click="exportCSV($event)"
          />
          <Button
            label="استيراد"
            icon="pi pi-file-import"
            severity="secondary"
            @click="importExcel()"
            :loading="loadImport"
            class="mr-2"
          />
          <!-- <Button
            label="تنفيذ الاستيراد"
            icon="pi pi-upload"
            class="mt-2"
            @click="importExcel()"
            :loading="loadImport"
          /> -->
        </template>
      </Toolbar>

      <DataTable
        class="bg-transparent transparent-table"
        tableStyle="min-width: 60rem background: transparent"
        v-model:expandedRows="expandedRows"
        :value="users.data"
        dataKey="id"
        v-if="!loadingUser"
        ref="dt"
        v-model:selection="selectedUsers"
        filterDisplay="row"
        :paginator="true"
        :rows="users.per_page"
        :totalRecords="users.total"
        :first="(current_page - 1) * users.per_page"
        :rowsPerPageOptions="[4, 8, 16]"
        :lazy="true"
        :sortField="sortField"
        :sortOrder="sortOrder"
        @page="onPageChange"
        @sort="onSort"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">المتاجر</h4>

            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="search" placeholder="بحث..." />
            </IconField>
          </div>
        </template>
        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column expander style="width: 5rem" />

        <!-- <Column expander style="width: 5rem" /> -->
        <Column field="store_name" header="اسم المتجر" sortable></Column>
        <Column field="name" header="اسم المدير" sortable></Column>
        <Column field="email" header="البريد الاكتروني" sortable></Column>

        <Column field="category.name" header="التصنيف" sortable>
          <!-- الفلتر -->
          <template #filter>
            <Select
              v-model="filter_type"
              :options="categoryList"
              filter
              optionLabel="name"
              placeholder="اختر التصنيف"
              class="w-full"
              :loading="loadList"
              fluid
              @change="showUsers(1, 'store')"
            >
              <template #value="slotProps">
                <div v-if="slotProps.value" class="flex items-center">
                  <div>{{ slotProps.value.name }}</div>
                </div>
              </template>
              <template #option="slotProps">
                <div class="flex items-center">
                  <div>{{ slotProps.option.name }}</div>
                </div>
              </template>
            </Select>
          </template>
        </Column>
        <Column header="صورة">
          <template #body="slotProps">
            <img
              :src="slotProps.data.profile_photo_path"
              :alt="slotProps.data.profile_photo_path"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>
        <!-- <Column field="products_count" header="عدد المنتجات" sortable></Column> -->
        <Column :exportable="false" field="عدد المنتجات" header="عدد المنتجات">
          <template #body="slotProps">
            <el-button type="success" class="text-sm" round>{{
              slotProps.data.products_count
            }}</el-button>
          </template>
        </Column>
        <Column :exportable="false" field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Button
                v-if="$can('تعديل مستخدم')"
                icon="pi pi-pencil"
                outlined
                rounded
                class="ml-2"
                @click="openEditModel(slotProps.data)"
              />
              <Button
                v-if="$can('حذف مستخدم')"
                icon="pi pi-trash"
                outlined
                rounded
                severity="danger"
                @click="opendeletemodel(slotProps.data)"
              />
              <FileUpload
                v-if="$can('اضافة منتج جديد')"
                mode="basic"
                id="image"
                @select="(event) => importCsv(event, slotProps.data.id)"
                customUpload
                auto
                severity="secondary"
                class="p-button-outlined mr-2"
                chooseLabel="استيراد"
              />
            </div>
          </template>
        </Column>

        <template #expansion="slotProps">
          <div v-if="slotProps.data.products" class="p-4">
            <h5>بيانات المنتجات</h5>
            <DataTable :value="slotProps.data.products">
              <Column field="id" header="رقم المنتج"></Column>
              <Column field="name" header="اسم المنتج"></Column>
              <Column field="description" header="الوصف"></Column>
              <Column field="Wholesale_price" header="سعر الجمله"></Column>
              <Column field="price" header="سعر البيع"></Column>
            </DataTable>
          </div>
        </template>
      </DataTable>

      <DataTable
        v-else
        class="bg-transparent transparent-table"
        tableStyle="min-width: 60rem background: transparent"
        :value="usersload"
        v-model:selection="selectedUsers"
        dataKey="id"
        :paginator="true"
        :rows="10"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">المتاجر</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <Skeleton
                Skeleton
                width="10rem"
                height="2rem"
                borderRadius="8px"
              ></Skeleton>
            </IconField>
          </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="اسم المتجر" header="اسم المتجر" style="min-width: 12rem">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
        <Column field="اسم المدير" header="اسم المدير" style="min-width: 12rem">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
        <Column
          field="البريد الاكتروني"
          header="البريد الاكتروني"
          style="min-width: 16rem"
        >
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
        <Column field="التصنيف" header="التصنيف" style="min-width: 16rem">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
        <Column header="الصورة">
          <template #body="slotProps">
            <Skeleton Skeleton width="10rem" height="4rem" borderRadius="8px"></Skeleton>
          </template>
        </Column>

        <Column field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Skeleton shape="circle" size="2.5rem" class="mr-2"></Skeleton>
              <Skeleton shape="circle" size="2.5rem" class="mr-2"></Skeleton>
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- اضافة وتعديل منتح -->
    <Dialog
      v-model:visible="dialogVisible"
      :style="{ width: '450px' }"
      :header="editMode ? 'تعديل بيانات متجر' : 'اضافة متجر'"
      :modal="true"
    >
      <div class="flex flex-col gap-6">
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-6">
            <label for="name" class="block font-bold mb-3">اسم صاحب المتجر</label>
            <InputText
              id="name"
              v-model="name"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="email" class="block font-bold mb-3">البريد الاكتروني</label>
            <InputText
              id="email"
              v-model="email"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="password" class="block font-bold mb-3">كلمة المرور</label>
            <InputText
              id="password"
              v-model="password"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="phone_number" class="block font-bold mb-3">رقم الهاتف</label>
            <InputText
              id="phone_number"
              v-model="phone_number"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="store_name" class="block font-bold mb-3"
              >اسم العلامة التجارية</label
            >
            <InputText
              id="store_name"
              v-model="store_name"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="description" class="block font-bold mb-3">وصف المتجر</label>
            <InputText
              id="description"
              v-model="description"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="location" class="block font-bold mb-3">عنوان المتجر</label>
            <InputText
              id="location"
              v-model="location"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="location_desc" class="block font-bold mb-3"
              >عنوان المتجر التفصيلي</label
            >
            <InputText
              id="location_desc"
              v-model="location_desc"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="category_id" class="block font-bold mb-3">تصنيف المنتجر</label>
            <Select
              v-model="category_id"
              :options="categoryList"
              filter
              optionLabel="name"
              placeholder="اختر التصنيف"
              class="w-full"
              :loading="loadList"
              fluid
            >
              <template #value="slotProps">
                <div v-if="slotProps.value" class="flex items-center">
                  <div>{{ slotProps.value.name }}</div>
                </div>
              </template>
              <template #option="slotProps">
                <div class="flex items-center">
                  <div>{{ slotProps.option.name }}</div>
                </div>
              </template>
            </Select>
          </div>
          <div class="col-span-6">
            <label for="role_id" class="block font-bold mb-3">دور المتجر</label>
            <Select
              v-model="role_id"
              :options="roleList"
              filter
              optionLabel="name"
              placeholder="اختر الدور"
              class="w-full"
              :loading="loadList"
              fluid
            >
              <template #value="slotProps">
                <div v-if="slotProps.value" class="flex items-center">
                  <div>{{ slotProps.value.name }}</div>
                </div>
              </template>
              <template #option="slotProps">
                <div class="flex items-center">
                  <div>{{ slotProps.option.name }}</div>
                </div>
              </template>
            </Select>
          </div>

          <div class="col-span-6">
            <label for="profile_photo_path" class="block font-bold mb-3"
              >صورة المنتجر</label
            >
            <FileUpload
              mode="basic"
              id="profile_photo_path"
              @select="onFileSelect"
              customUpload
              auto
              severity="secondary"
              v-model="profile_photo_path"
              class="p-button-outlined"
            />
            <img
              v-if="src"
              :src="src"
              alt="Image"
              class="shadow-md rounded-xl w-full sm:w-64"
              style="filter: grayscale(100%)"
            />
          </div>
        </div>
      </div>

      <template #footer>
        <Button label="إلغاء" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button
          :loading="loadingSubmitUser"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="editMode ? UpdateUser() : AddUser()"
        />
      </template>
    </Dialog>
    <!-- حذف صادر -->
    <Dialog
      v-model:visible="dialogDelete"
      :style="{ width: '450px' }"
      header="تأكيد"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle" />
        <span v-if="id"
          >هل أنت متأكد من أنك تريد حذف الخدمة صاحب التعريف <b> {{ id }}</b> ؟</span
        >
      </div>
      <template #footer>
        <Button label="لا" icon="pi pi-times" text @click="dialogDelete = false" />
        <Button
          label="نعم"
          icon="pi pi-check"
          :loading="loadingSubmitUser"
          @click="deleteUser"
        />
      </template>
    </Dialog>
    <!-- حذف اختيار صادر -->
    <Dialog
      v-model:visible="dialogDeleteSelect"
      :style="{ width: '450px' }"
      header="تأكيد"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle" />
        <span v-if="ids" class="font-bold"
          >هل أنت متأكد أنك تريد حذف المشاريع المحددة؟</span
        >
      </div>

      <template #footer>
        <Button label="لا" icon="pi pi-times" text @click="dialogDeleteSelect = false" />
        <Button
          label="نعم"
          icon="pi pi-check"
          text
          :loading="loadingSubmitUser"
          @click="deleteSelected"
        />
      </template>
    </Dialog>
  </AppLayout>
</template>

<style scoped>
/* الجدول نفسه */
.transparent-table :deep(.p-datatable-table),
.transparent-table :deep(.p-datatable-thead > tr > th),
.transparent-table :deep(.p-datatable-tbody > tr > td),
.transparent-table :deep(.p-datatable-header),
.transparent-table :deep(.p-datatable-footer) {
  background: transparent !important;
}

/* الصفوف (السجلات) */
.transparent-table :deep(.p-datatable-tbody > tr) {
  background: transparent !important;
}

/* Pagination */
.transparent-table :deep(.p-paginator),
.transparent-table :deep(.p-paginator-page),
.transparent-table :deep(.p-paginator-first),
.transparent-table :deep(.p-paginator-prev),
.transparent-table :deep(.p-paginator-next),
.transparent-table :deep(.p-paginator-last),
.transparent-table :deep(.p-dropdown),
.transparent-table :deep(.p-dropdown-panel),
.transparent-table :deep(.p-inputtext) {
  background: transparent !important;
}

/* إزالة hover الافتراضي */
.transparent-table :deep(.p-datatable-tbody > tr:hover) {
  background: rgba(255, 255, 255, 0.05) !important;
}

/* إزالة الخط الأبيض أسفل الجدول */
.transparent-table :deep(.p-datatable-wrapper) {
  background: transparent !important;
}
</style>
