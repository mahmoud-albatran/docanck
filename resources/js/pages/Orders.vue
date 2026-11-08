<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { OrderStore } from "../stores/Orders";
const {
  dialogVisible,
  editMode,
  dialogDelete,
  dialogDeleteSelect,
  loadingSubmitOrder,
  loadingOrder,
  orders,
  id,
  message,
  status,
  search,
  per_page,
  sortField,
  sortOrder,
  selectedOrders,
  ids,
  loadList,
  current_page,
} = storeToRefs(OrderStore());
const {
  showOrders,
  AddSubCategory,
  opendeletemodel,
  openaddmodel,
  openEditModel,
  UpdateOrder,
  deleteOrder,
  opendeleteSelect,
  deleteSelected,
  getSelect,
} = OrderStore();
onMounted(() => {
  showOrders();
  //   getSelect();
});
const expandedRows = ref({});

// =================== items Skelton ===================
const ordersload = ref(new Array(4));

// =================== Paginater ===================
const onPageChange = (event) => {
  const newPage = event.page + 1; // page يبدأ من 0
  per_page.value = event.rows;

  showOrders(newPage);
};

// =================== Sorting ===================
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  showOrders(1);
};

// =================== Searching ===================
let searchTimer = null;

watch(search, (value) => {
  // إلغاء أي مؤقت سابق
  clearTimeout(searchTimer);

  // إنشاء مؤقت جديد
  searchTimer = setTimeout(() => {
    showOrders(1);
  }, 1000); // ← التأخير 1000ms || 1s
});

const statusList = ref([
  { name: "قيد الانتظار", valuee: "pending" },
  { name: "تم الشحن", valuee: "shipped" },
  { name: "مكتمل", valuee: "complete" },
  { name: "ملغاه", valuee: "cancel" },
]);
</script>

<template>
  <AppLayout>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <!-- v-if="$can('اضافة منتج جديد')" -->
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
            :disabled="!selectedOrders || !selectedOrders.length"
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
        :value="orders.data"
        dataKey="id"
        v-if="!loadingOrder"
        ref="dt"
        v-model:selection="selectedOrders"
        filterDisplay="row"
        :paginator="true"
        :rows="orders.per_page"
        :totalRecords="orders.total"
        :first="(current_page - 1) * orders.per_page"
        :rowsPerPageOptions="[4, 8, 16]"
        :lazy="true"
        :sortField="sortField"
        :sortOrder="sortOrder"
        @page="onPageChange"
        @sort="onSort"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">طلبات المستخدمين</h4>

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

        <Column field="id" header="رقم الطلب" sortable></Column>
        <Column field="user.name" header="اسم الزبون" sortable></Column>
        <Column field="adress.name" header="عنوان التوصيل" sortable></Column>
        <Column field="total_price" header="السعر الكلي" sortable></Column>
        <Column field="Price_before_discount" header="السعر قبل الخصم" sortable></Column>
        <Column field="status" header="حالة الطلب" sortable></Column>

        <Column :exportable="false" field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Button
                v-if="$can('تغير حالة الطلب والرد على العملاء')"
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

        <template #expansion="slotProps">
          <div v-if="slotProps.data.order_items" class="p-4">
            <h5>بيانات الصرف</h5>
            <DataTable :value="slotProps.data.order_items">
              <Column header="صورة المنتج">
                <template #body="slotProps">
                  <img
                    :src="slotProps.data.product.image"
                    :alt="slotProps.data.product.image"
                    class="rounded"
                    style="width: 64px"
                  />
                </template>
              </Column>
              <!-- <Column field="product.image" header="صورة المنتج"></Column> -->
              <Column field="product.id" header="رقم المنتح"></Column>
              <Column field="product.name" header="اسم المنتح"></Column>
              <Column field="quantity" header="الكمية"></Column>
              <Column field="product.user.name" header="اسم المتجر"></Column>
              <Column field="product.user.phone_number" header="رقم هاتف المتجر"></Column>
              <Column field="unit_price" header="سعر الوحدة"></Column>
            </DataTable>
          </div>
          <div v-if="slotProps.data.adress" class="p-4">
            <h5>بيانات العميل</h5>
            <DataTable :value="[slotProps.data.adress]">
              <Column field="user.name" header="اسم العميل"></Column>
              <Column field="user.phone_number" header="رقم الهاتف"></Column>
              <Column field="name" header="عنوان التوصيل"></Column>
              <Column field="address" header="عنوان التوصيل"></Column>
            </DataTable>
          </div>
          <div v-if="slotProps.data.code" class="p-4">
            <h5>الخصم</h5>
            <DataTable :value="[slotProps.data.code]">
              <Column field="code" header="كود الخصم"></Column>
              <Column field="Percentage" header="النسبة"></Column>
            </DataTable>
          </div>
        </template>
      </DataTable>

      <DataTable
        v-else
        class="bg-transparent transparent-table"
        tableStyle="min-width: 60rem background: transparent"
        :value="ordersload"
        v-model:selection="selectedOrders"
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
          <div class="col-span-12">
            <label for="message" class="block font-bold mb-3">الرسالة</label>
            <InputText
              id="message"
              v-model="message"
              required="true"
              rows="3"
              cols="0"
              fluid
            />
          </div>

          <div class="col-span-12">
            <label for="status" class="block font-bold mb-3">حالة الطلب</label>
            <Select
              v-model="status"
              :options="statusList"
              filter
              optionLabel="name"
              placeholder="اختر التصنيف"
              class="w-full"
              :loading="loadList"
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
      </div>

      <template #footer>
        <Button label="إلغاء" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button
          :loading="loadingSubmitOrder"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="editMode ? UpdateOrder() : AddSubCategory()"
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
          :loading="loadingSubmitOrder"
          @click="deleteOrder"
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
          :loading="loadingSubmitOrder"
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
