<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { PermissionStore } from "../stores/permissions";
const {
  isAddMode,
  dialogVisible,
  editMode,
  dialogDelete,
  dialogDeleteSelect,
  loadingSubmitPermission,
  loadingPermission,
  Permissions,
  id,
  name,
  guard_name,
  search,
  per_page,
  sortField,
  sortOrder,
  selectedCategories,
  ids,
  current_page,
} = storeToRefs(PermissionStore());
const {
  showPermission,
  AddPermission,
  opendeletemodel,
  openaddmodel,
  openEditModel,
  UpdatePermission,
  deletePermission,
  opendeleteSelect,
  deleteSelected,
} = PermissionStore();
onMounted(() => {
  showPermission();
});

// =================== items Skelton ===================
const Permissionsload = ref(new Array(4));

// =================== Paginater ===================
const onPageChange = (event) => {
  const newPage = event.page + 1; // page يبدأ من 0
  per_page.value = event.rows;

  showPermission(newPage);
};

// =================== Sorting ===================
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  showPermission(1);
};

// =================== Searching ===================
let searchTimer = null;

watch(search, (value) => {
  // إلغاء أي مؤقت سابق
  clearTimeout(searchTimer);

  // إنشاء مؤقت جديد
  searchTimer = setTimeout(() => {
    showPermission(1);
  }, 1000); // ← التأخير 1000ms || 1s
});

const dt = ref();

function exportCSV() {
  dt.value.exportCSV();
}

const statusList = ref([
  { name: "web", valuee: "web" },
  { name: "api", valuee: "api" },
]);
</script>

<template>
  <AppLayout>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button
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
            :disabled="!selectedCategories || !selectedCategories.length"
          />
        </template>

        <template #end>
          <Button
            label="تصدير"
            icon="pi pi-upload"
            severity="secondary"
            @click="exportCSV($event)"
          />
        </template>
      </Toolbar>

      <DataTable
        v-if="!loadingPermission"
        ref="dt"
        :value="Permissions.data"
        dataKey="id"
        v-model:selection="selectedCategories"
        :paginator="true"
        :rows="Permissions.per_page"
        :totalRecords="Permissions.total"
        :first="(current_page - 1) * Permissions.per_page"
        :rowsPerPageOptions="[4, 8, 16]"
        :lazy="true"
        :sortField="sortField"
        :sortOrder="sortOrder"
        @page="onPageChange"
        @sort="onSort"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">الصلاحيات</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="search" placeholder="بحث..." />
            </IconField>
          </div>
        </template>

        <!-- <template #footer>
          <Paginator
            :rows="Permissions.per_page"
            :totalRecords="Permissions.total"
            :first="(Permissions.current_page - 1) * Permissions.per_page"
            :rowsPerPageOptions="[4, 8, 16]"
            @page="onPageChange"
          >
          </Paginator>
        </template> -->

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="id" header="ID" sortable style="min-width: 12rem"></Column>
        <Column field="name" header="الاسم" sortable style="min-width: 12rem"></Column
        ><Column
          field="guard_name"
          header="اسم القارد"
          sortable
          style="min-width: 16rem"
        ></Column>

        <Column :exportable="false" field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Button
                icon="pi pi-pencil"
                outlined
                rounded
                class="ml-2"
                @click="openEditModel(slotProps.data)"
              />
              <Button
                icon="pi pi-trash"
                outlined
                rounded
                severity="danger"
                @click="opendeletemodel(slotProps.data)"
              />
            </div>
          </template>
        </Column>
      </DataTable>

      <DataTable
        v-else
        ref="dt"
        :value="Permissionsload"
        v-model:selection="selectedCategories"
        dataKey="id"
        :paginator="true"
        :rows="10"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">الصلاحيات</h4>
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
        <Column field="الاسم" header="الاسم" sortable style="min-width: 12rem">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
        <Column field="الوصق" header="الوصق" sortable style="min-width: 16rem">
          <template #body>
            <Skeleton></Skeleton>
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

    <Dialog
      v-model:visible="dialogVisible"
      :style="{ width: '450px' }"
      :header="editMode ? 'تعديل صلاحية' : 'اضافة صلاحية'"
      :modal="true"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label for="name" class="block font-bold mb-3">اسم الصلاحية</label>
          <InputText id="name" v-model="name" required="true" fluid />
        </div>
        <div>
          <label for="guard_name" class="block font-bold mb-3">القارد</label>
          <Select
            v-model="guard_name"
            :options="statusList"
            filter
            optionLabel="name"
            placeholder="اختر القارد"
            class="w-full"
            fluid
          >
            <template #value="slotProps">
              <div v-if="slotProps.value" class="flex items-center">
                <div>{{ slotProps.value.valuee }}</div>
              </div>
            </template>
            <template #option="slotProps">
              <div class="flex items-center">
                <div>{{ slotProps.option.name }}</div>
              </div>
            </template>
          </Select>
        </div>
      </div>

      <template #footer>
        <Button label="إلغاء" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button
          :loading="loadingSubmitPermission"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="editMode ? UpdatePermission() : AddPermission()"
        />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="dialogDelete"
      :style="{ width: '450px' }"
      header="تأكيد"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle" />
        <span v-if="id"
          >هل أنت متأكد من أنك تريد حذف التصنيف صاحب التعريف <b> {{ id }}</b> ؟</span
        >
      </div>
      <template #footer>
        <Button label="لا" icon="pi pi-times" text @click="dialogDelete = false" />
        <Button
          label="نعم"
          icon="pi pi-check"
          :loading="loadingSubmitPermission"
          @click="deletePermission"
        />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="dialogDeleteSelect"
      :style="{ width: '450px' }"
      header="تأكيد"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle" />
        <span v-if="ids" class="font-bold"
          >هل أنت متأكد أنك تريد حذف التصنيفات المحددة؟</span
        >
      </div>

      <template #footer>
        <Button label="لا" icon="pi pi-times" text @click="dialogDeleteSelect = false" />
        <Button
          label="نعم"
          icon="pi pi-check"
          text
          :loading="loadingSubmitPermission"
          @click="deleteSelected"
        />
      </template>
    </Dialog>
  </AppLayout>
</template>
