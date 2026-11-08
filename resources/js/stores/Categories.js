import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';

export const CategoryStore = defineStore("CategoryData",{
    state : () => ({

      toast: useToast(),
      loadingCategory: false,
      categories: [],

      loadingSubmitCategory: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedCategories: [],
      ids: null,


      src: '',
    //   data
      id: '',
      name: '',
      icon: '',
      description: '',


        // paginate
      current_page: 1,

    //   mode
    isAddMode: false,
    dialogVisible: false,
    editMode: false,


    // for delete mode
    dialogDelete: false,

    // for delete select mode
    dialogDeleteSelect: false

    }),

    getters: {

    },

    actions: {

        async showCategories(current_page){

           this.loadingCategory = true
           let currentPage = current_page ?? this.current_page;
           this.current_page = currentPage;

           try {
                const response = await http().get('api/categories', {
                    params: {
                        page: this.current_page,
                        search: this.search,
                        per_page: this.per_page,
                        sort_field: this.sortField,
                        sort_order: this.sortOrder
                    }
                });
                this.categories = response.data.categories;
            } catch (error) {
                // الخطأ سيظهر تلقائياً عبر الـ Interceptor
            } finally {
                this.loadingCategory = false;
            }
        },
        async AddCategory(){
           this.loadingSubmitCategory = true
           const formData = this.prepareFormData();
           try {
                const response = await http().post('api/categories', formData);

                this.toast.add({
                    severity: "success",
                    summary: "تمت العملية",
                    detail: response.data.message,
                    life: 3000,
                });

                this.closeDialogs();
                await this.showCategories();
                this.resetFormData();
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitCategory = false;
            }
        },
        async UpdateCategory(){
            this.loadingSubmitCategory = true
            const formData = this.prepareFormData();
            formData.append('_method', 'put');

            try {
                const response = await http().post(`api/categories/${this.id}`, formData);

                ElNotification({
                    title: 'نجاح',
                    message: response.data.message,
                    type: 'success',
                });

                this.closeDialogs();
                await this.showCategories();
                this.resetFormData();
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitCategory = false;
            }
        },
        async deleteCategory(){
            this.loadingSubmitCategory = true
            try {
                const response = await http().delete(`api/categories/${this.id}`);

                ElNotification({ title: 'نجاح', message: response.data.message, type: 'success' });

                this.dialogDelete = false;
                await this.showCategories();
                this.id = '';
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitCategory = false;
            }
        },
        async deleteSelected(){
            this.loadingSubmitCategory = true
            try {
                const response = await http().post('api/categories/delete-multiple', { ids: this.ids });

                this.toast.add({
                    severity: "success",
                    summary: "تم الحذف",
                    detail: response.data.message,
                    life: 3000,
                });

                this.dialogDeleteSelect = false;
                this.selectedCategories = [];
                this.ids = null;
                await this.showCategories();
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitCategory = false;
            }
        },
        // --- Helper Functions (دوال مساعدة لتنظيف الكود) ---
        prepareFormData() {
            let form_data = new FormData();
            if (this.name) form_data.append('name', this.name);
            if (this.description) form_data.append('description', this.description);
            if (this.icon) form_data.append('icon', this.icon);
            return form_data;
        },
        closeDialogs() {
            this.isAddMode = false;
            this.dialogVisible = false;
            this.editMode = false;
        },
        resetFormData(){
            this.id= null
            this.name= null
            this.description= null
            this.icon= null
            this.src= null
        },
        onFileSelect(event) {
            const file = event.files[0];
            this.icon = file;
            const reader = new FileReader();

            reader.onload = async (e) => {
                this.src = e.target.result;
            };

            reader.readAsDataURL(file);
        },
        openaddmodel(){
            this.resetFormData();
            this.isAddMode = true;
            this.dialogVisible = true;
            this.editMode = false;
        },
        openEditModel(category){
            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;
            // data edit
            this.id= category.id
            this.name= category.name
            this.description= category.description
            this.icon= category.icon
            this.src= category.icon
        },
        opendeletemodel(category){
            this.dialogDelete = true;

            this.id= category.id
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedCategories.map(item => item.id);
        },
    }

})
