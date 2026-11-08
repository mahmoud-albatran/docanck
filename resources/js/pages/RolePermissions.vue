<script setup>
import { onMounted, ref, watch } from "vue";
import Skeleton from "primevue/skeleton";
import Paginator from "primevue/paginator";
import AppLayout from "@/layout/AppLayout.vue";
import FileUpload from "primevue/fileupload";
import { storeToRefs } from "pinia";
import { RolePermissionStore } from "../stores/rolepermissions";
const { loadingRolePermission, permissions, roleId } = storeToRefs(RolePermissionStore());
const { showRolePermission, storeRolePermission } = RolePermissionStore();
import { useRoute } from "vue-router";
const route = useRoute();
onMounted(() => {
  showRolePermission(route.params.id);
});

// =================== items Skelton ===================
const Permissionsload = ref(new Array(4));

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
          <!-- <Button
            label="حذف"
            icon="pi pi-trash"
            severity="secondary"
            @click="opendeleteSelect"
            :disabled="!selectedCategories || !selectedCategories.length"
          /> -->
        </template>

        <template #end>
          <!-- <Button
            label="تصدير"
            icon="pi pi-upload"
            severity="secondary"
            @click="exportCSV($event)"
          /> -->
        </template>
      </Toolbar>

      <DataTable
        v-if="!loadingRolePermission"
        ref="dt"
        :value="permissions"
        dataKey="id"
        :lazy="true"
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
            <div class="icheck-primary d-inline">
              <input
                v-if="slotProps.data.active"
                type="checkbox"
                :id="'permission_' + slotProps.data.id"
                @change="storeRolePermission(roleId, slotProps.data.id)"
                checked
              />
              <input
                v-else
                type="checkbox"
                :id="'permission_' + slotProps.data.id"
                @change="storeRolePermission(roleId, slotProps.data.id)"
              />
              <label :for="'permission_' + slotProps.data.id"></label>
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
