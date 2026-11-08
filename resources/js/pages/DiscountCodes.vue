<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { DiscountCodeStore } from "../stores/DiscountCodes";
DiscountCodeStore;
const {
  dialogVisible,
  editMode,
  dialogDelete,
  dialogDeleteSelect,
  loadingSubmitDiscountCode,
  loadingDiscountCode,
  discountcodes,
  id,
  code,
  Percentage,
  search,
  per_page,
  sortField,
  sortOrder,
  selectedSubCategorys,
  ids,
  current_page,
} = storeToRefs(DiscountCodeStore());
const {
  showDiscountCodes,
  AddDiscountCode,
  opendeletemodel,
  openaddmodel,
  openEditModel,
  UpdateDiscountCode,
  deleteDiscountCode,
  opendeleteSelect,
  deleteSelected,
} = DiscountCodeStore();
onMounted(() => {
  showDiscountCodes();
});
const expandedRows = ref({});

// =================== items Skelton ===================
const discountcodesload = ref(new Array(4));

// =================== Paginater ===================
const onPageChange = (event) => {
  const newPage = event.page + 1; // page يبدأ من 0
  per_page.value = event.rows;

  showDiscountCodes(newPage);
};

// =================== Sorting ===================
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  showDiscountCodes(1);
};

// =================== Searching ===================
let searchTimer = null;

watch(search, (value) => {
  // إلغاء أي مؤقت سابق
  clearTimeout(searchTimer);

  // إنشاء مؤقت جديد
  searchTimer = setTimeout(() => {
    showDiscountCodes(1);
  }, 1000); // ← التأخير 1000ms || 1s
});
</script>

<template>
  <AppLayout>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button
            v-if="$can('اضافة اكواد خصم')"
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
            :disabled="!selectedSubCategorys || !selectedSubCategorys.length"
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
        class="bg-transparent transparent-table"
        tableStyle="min-width: 60rem background: transparent"
        v-model:expandedRows="expandedRows"
        :value="discountcodes.data"
        dataKey="id"
        v-if="!loadingDiscountCode"
        ref="dt"
        v-model:selection="selectedSubCategorys"
        filterDisplay="row"
        :paginator="true"
        :rows="discountcodes.per_page"
        :totalRecords="discountcodes.total"
        :first="(current_page - 1) * discountcodes.per_page"
        :rowsPerPageOptions="[2, 4, 8, 16]"
        :lazy="true"
        :sortField="sortField"
        :sortOrder="sortOrder"
        @page="onPageChange"
        @sort="onSort"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">التصنيفات الفرعية</h4>

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
        <Column field="id" header="ID" sortable style="min-width: 12rem"></Column>
        <Column field="code" header="الكود" sortable></Column>
        <Column field="Percentage" header="النسبة" sortable></Column>

        <Column :exportable="false" field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Button
                v-if="$can('تعديل اكواد خصم')"
                icon="pi pi-pencil"
                outlined
                rounded
                class="ml-2"
                @click="openEditModel(slotProps.data)"
              />
              <Button
                v-if="$can('حذف اكواد خصم')"
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
        class="bg-transparent transparent-table"
        tableStyle="min-width: 60rem background: transparent"
        :value="discountcodesload"
        v-model:selection="selectedSubCategorys"
        dataKey="id"
        :paginator="true"
        :rows="10"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">الخدمات</h4>
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
        <Column field="الاسم" header="الاسم" style="min-width: 12rem">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>

        <Column header="الاصناف">
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
      :header="editMode ? 'تعديل تصنيف فرعي' : ' اضافة تصنيف فرعي '"
      :modal="true"
    >
      <div class="flex flex-col gap-6">
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-6">
            <label for="code" class="block font-bold mb-3">ادخل الكود</label>
            <InputText
              id="code"
              v-model="code"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="Percentage" class="block font-bold mb-3">النسبه</label>
            <InputText
              id="Percentage"
              v-model="Percentage"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
        </div>
      </div>

      <template #footer>
        <Button label="إلغاء" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button
          :loading="loadingSubmitDiscountCode"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="editMode ? UpdateDiscountCode() : AddDiscountCode()"
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
          :loading="loadingSubmitDiscountCode"
          @click="deleteDiscountCode"
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
          >هل أنت متأكد أنك تريد حذف التصنيفات الفرعية المحددة؟</span
        >
      </div>

      <template #footer>
        <Button label="لا" icon="pi pi-times" text @click="dialogDeleteSelect = false" />
        <Button
          label="نعم"
          icon="pi pi-check"
          text
          :loading="loadingSubmitDiscountCode"
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
