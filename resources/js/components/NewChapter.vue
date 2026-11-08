<script setup>
import { ref } from "vue";

const emit = defineEmits(["openaddmodel", "AddChapter", "update:name"]);

function openadd() {
  emit("openaddmodel");
}
function callParentAdd() {
  emit("AddChapter");
}

const props = defineProps({
  number: [String, Number],
  title: String,
  name: String,
  isAddMode: Boolean,
  loadingSubmitChapter: Boolean,
});

const open = ref(false);

const menu = ref(null);

const actions = ref([
  {
    label: "Refresh",
    icon: "pi pi-refresh",
  },
  {
    label: "Search",
    icon: "pi pi-search",
  },
  {
    separator: true,
  },
  {
    label: "Delete",
    icon: "pi pi-times",
  },
]);

const toggle = (event) => {
  menu.value.toggle(event);
};
</script>

<template>
  <div
    class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden mt-5"
  >
    <!-- HEADER -->
    <button
      class="w-full flex justify-between items-center px-5 py-4 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 font-bold text-gray-700 dark:text-gray-100 transition-all"
    >
      <div class="flex items-center gap-3">
        <div
          class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center"
        >
          {{ number }}
        </div>

        <span v-if="!isAddMode">{{ title }}</span>
        <InputText
          v-if="isAddMode"
          id="salary"
          :value="props.name"
          @input="emit('update:name', $event.target.value)"
          required="true"
          placeholder="اضافة شابتر جديد"
          fluid
        />
      </div>

      <div class="flex items-center gap-2">
        <Button
          v-if="!isAddMode"
          label="جديد"
          icon="pi pi-plus"
          severity="secondary"
          class="ml-2"
          @click="openadd"
        />

        <Button
          v-else
          :loading="loadingSubmitChapter"
          label="حفظ"
          icon="pi pi-check"
          @click.prevent="callParentAdd()"
        />
      </div>
    </button>
  </div>
</template>
