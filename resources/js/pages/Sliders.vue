<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { SliderStore } from "../stores/Sliders";
const {
  isAddMode,
  dialogVisible,
  editMode,
  dialogDelete,
  dialogDeleteSelect,
  loadingSubmitSlider,
  loadingSlider,
  sliders,
  id,
  image,
  src,
  search,
  per_page,
  sortField,
  sortOrder,
  selectedSliders,
  ids,
  current_page,
} = storeToRefs(SliderStore());
const {
  showSliders,
  AddSlider,
  opendeletemodel,
  openaddmodel,
  openEditModel,
  UpdateSlider,
  deleteSlider,
  onFileSelect,
  opendeleteSelect,
  deleteSelected,
} = SliderStore();
onMounted(() => {
  showSliders();
});

// =================== items Skelton ===================
const slidersload = ref(new Array(4));

// =================== Paginater ===================
const onPageChange = (event) => {
  const newPage = event.page + 1; // page يبدأ من 0
  per_page.value = event.rows;

  showSliders(newPage);
};

// =================== Sorting ===================
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  showSliders();
};

// =================== Searching ===================
let searchTimer = null;

watch(search, (value) => {
  // إلغاء أي مؤقت سابق
  clearTimeout(searchTimer);

  // إنشاء مؤقت جديد
  searchTimer = setTimeout(() => {
    showSliders();
  }, 1000); // ← التأخير 1000ms || 1s
});

const dt = ref();

function exportCSV() {
  dt.value.exportCSV();
}
</script>

<template>
  <AppLayout>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button
            v-if="$can('اضافة اعلان')"
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
            :disabled="!selectedSliders || !selectedSliders.length"
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
        v-if="!loadingSlider"
        ref="dt"
        :value="sliders.data"
        dataKey="id"
        v-model:selection="selectedSliders"
        :paginator="true"
        :rows="sliders.per_page"
        :totalRecords="sliders.total"
        :first="(current_page - 1) * sliders.per_page"
        :rowsPerPageOptions="[2, 4, 8, 16]"
        :lazy="true"
        :sortField="sortField"
        :sortOrder="sortOrder"
        @page="onPageChange"
        @sort="onSort"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">التصنيفات</h4>
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
            :rows="sliders.per_page"
            :totalRecords="sliders.total"
            :first="(sliders.current_page - 1) * sliders.per_page"
            :rowsPerPageOptions="[4, 8, 16]"
            @page="onPageChange"
          >
          </Paginator>
        </template> -->

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

        <Column header="ايقونة">
          <template #body="slotProps">
            <img
              :src="slotProps.data.image"
              :alt="slotProps.data.image"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>

        <Column :exportable="false" field="الاعدادات" header="الاعدادات">
          <template #body="slotProps">
            <div class="flex">
              <Button
                v-if="$can('تعديل اعلان')"
                icon="pi pi-pencil"
                outlined
                rounded
                class="ml-2"
                @click="openEditModel(slotProps.data)"
              />
              <Button
                v-if="$can('حذف اعلان')"
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
        :value="slidersload"
        v-model:selection="selectedSliders"
        dataKey="id"
        :paginator="true"
        :rows="10"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">التصنيفات</h4>
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
        <Column header="أيقونة">
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

    <Dialog
      v-model:visible="dialogVisible"
      :style="{ width: '450px' }"
      :header="editMode ? 'تعديل تصنيف' : 'اضافة تصنيف'"
      :modal="true"
    >
      <div class="flex flex-col gap-6">
        <!-- <img
          src="https://d11p0alxbet5ud.cloudfront.net/Pictures/1024x536/4/8/2/1417482_img_243663.jpg"
          class="block m-auto pb-4"
        /> -->

        <div>
          <label for="image" class="block font-bold mb-3">Inventory Status</label>
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
      </div>

      <template #footer>
        <Button label="إلغاء" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button
          :loading="loadingSubmitSlider"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="editMode ? UpdateSlider() : AddSlider()"
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
          :loading="loadingSubmitSlider"
          @click="deleteSlider"
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
          :loading="loadingSubmitSlider"
          @click="deleteSelected"
        />
      </template>
    </Dialog>
  </AppLayout>
</template>
