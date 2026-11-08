<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { ProductStore } from "../stores/Products";
const {
  dialogVisible,
  editMode,
  dialogDelete,
  dialogDeleteSelect,
  loadingSubmitProduct,
  loadingProduct,
  products,
  id,
  name,
  description,
  price,
  type,
  count,
  image,
  src,
  category_id,
  sub_category_id,
  user_id,
  search,
  per_page,
  sortField,
  sortOrder,
  selectedProducts,
  ids,
  loadList,
  storeList,
  subCategoryList,
  categoryList,
  filter_type,
  current_page,
} = storeToRefs(ProductStore());
const {
  showProducts,
  AddProduct,
  opendeletemodel,
  openaddmodel,
  openEditModel,
  UpdateProduct,
  deleteProduct,
  onFileSelect,
  opendeleteSelect,
  deleteSelected,
  getSelect,
} = ProductStore();
onMounted(() => {
  showProducts();
  getSelect();
});
const expandedRows = ref({});

// =================== items Skelton ===================
const productsload = ref(new Array(4));

// =================== Paginater ===================
const onPageChange = (event) => {
  const newPage = event.page + 1; // page يبدأ من 0
  per_page.value = event.rows;

  showProducts(newPage);
};

// =================== Sorting ===================
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  showProducts(1);
};

// =================== Searching ===================
let searchTimer = null;

watch(search, (value) => {
  // إلغاء أي مؤقت سابق
  clearTimeout(searchTimer);

  // إنشاء مؤقت جديد
  searchTimer = setTimeout(() => {
    showProducts(1);
  }, 1000); // ← التأخير 1000ms || 1s
});
</script>

<template>
  <AppLayout>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button
            v-if="$can('اضافة منتج جديد')"
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
            :disabled="!selectedProducts || !selectedProducts.length"
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
        :value="products.data"
        dataKey="id"
        v-if="!loadingProduct"
        ref="dt"
        v-model:selection="selectedProducts"
        filterDisplay="row"
        :paginator="true"
        :rows="products.per_page"
        :totalRecords="products.total"
        :first="(current_page - 1) * products.per_page"
        :rowsPerPageOptions="[4, 8, 16]"
        :lazy="true"
        :sortField="sortField"
        :sortOrder="sortOrder"
        @page="onPageChange"
        @sort="onSort"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">المنتجات</h4>

            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="search" placeholder="بحث..." />
            </IconField>
          </div>
        </template>
        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

        <!-- <Column expander style="width: 5rem" /> -->
        <Column field="id" header="رقم المنتج" sortable></Column>
        <Column field="name" header="اسم المنتج" sortable></Column>
        <Column field="description" header="الوصف" sortable></Column>

        <Column field="category.name" header="التصنيف" sortable>
          <!-- الفلتر -->
          <template #filter>
            <!-- <Select
              v-model="filter_type"
              :options="categoryList['name']"
              placeholder="اختر النوع"
              class="w-full"
              @change="showProducts(1)"
            /> -->
            <Select
              v-model="filter_type"
              :options="categoryList"
              filter
              optionLabel="name"
              placeholder="اختر التصنيف"
              class="w-full"
              :loading="loadList"
              fluid
              @change="showProducts(1)"
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
        <Column header="ايقونة">
          <template #body="slotProps">
            <!-- :src="`storage/${slotProps.data.image}`" -->
            <img
              :src="slotProps.data.image"
              :alt="slotProps.data.image"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>
        <Column field="price" header="سعر البيع" sortable></Column>
        <Column field="type" header="نوع المنتج" sortable></Column>
        <Column field="Wholesale_price" header="سعر الجملة" sortable></Column>

        <Column :exportable="false" field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Button
                v-if="$can('تعديل منتج')"
                icon="pi pi-pencil"
                outlined
                rounded
                class="ml-2"
                @click="openEditModel(slotProps.data)"
              />
              <Button
                v-if="$can('حذف منتج')"
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
        :value="productsload"
        v-model:selection="selectedProducts"
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
        <Column field="النوع" header="النوع" style="min-width: 16rem">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
        <Column field="مكان الاستلام" header="مكان الاستلام" style="min-width: 16rem">
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
      :header="editMode ? 'تعديل منتج' : 'اضافة منتج'"
      :modal="true"
    >
      <div class="flex flex-col gap-6">
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-6">
            <label for="name" class="block font-bold mb-3">اسم المنتج</label>
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
            <label for="description" class="block font-bold mb-3">الوصف</label>
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
            <label for="price" class="block font-bold mb-3">السعر</label>
            <InputText
              id="price"
              v-model="price"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>
          <div class="col-span-6">
            <label for="count" class="block font-bold mb-3">عدد الكمية المتوفر</label>
            <InputText
              id="count"
              v-model="count"
              required="true"
              rows="3"
              cols="20"
              fluid
            />
          </div>

          <div class="col-span-6">
            <label for="image" class="block font-bold mb-3">صورة المنتج</label>
            <FileUpload
              mode="basic"
              id="image"
              @select="onFileSelect"
              customUpload
              auto
              severity="secondary"
              v-model="image"
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
          <div class="col-span-6">
            <label for="type" class="block font-bold mb-3">نوع ملف </label>
            <div class="grid grid-cols-12 gap-4">
              <div class="flex items-center gap-2 col-span-3">
                <RadioButton v-model="type" inputId="new" name="new" value="new" />
                <!-- @click="getSelect('representative')" -->
                <label for="new">جديد</label>
              </div>
              <div class="flex items-center gap-2 col-span-3">
                <RadioButton v-model="type" inputId="old" name="old" value="old" />
                <label for="old">مستعمل</label>
              </div>
            </div>
          </div>
        </div>

        <div>
          <label for="category_id" class="block font-bold mb-3">التصنيف</label>
          <Select
            v-model="category_id"
            :options="categoryList"
            filter
            optionLabel="name"
            placeholder="اختر التصنيف"
            class="w-full"
            :loading="loadList"
            fluid
            @change="getSelect(category_id)"
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
        <div>
          <label for="sub_category_id" class="block font-bold mb-3">التصنيف الفرعي</label>
          <Select
            v-model="sub_category_id"
            :options="subCategoryList"
            filter
            optionLabel="name"
            placeholder="اختر التصنيف الفرعي"
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
        <div>
          <label for="user_id" class="block font-bold mb-3">المتجر</label>
          <Select
            v-model="user_id"
            :options="storeList"
            filter
            optionLabel="name"
            placeholder="اختر المندوب"
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
      </div>

      <template #footer>
        <Button label="إلغاء" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button
          :loading="loadingSubmitProduct"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="editMode ? UpdateProduct() : AddProduct()"
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
          :loading="loadingSubmitProduct"
          @click="deleteProduct"
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
          >هل أنت متأكد أنك تريد حذف المنتجات المحددة؟</span
        >
      </div>

      <template #footer>
        <Button label="لا" icon="pi pi-times" text @click="dialogDeleteSelect = false" />
        <Button
          label="نعم"
          icon="pi pi-check"
          text
          :loading="loadingSubmitProduct"
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
