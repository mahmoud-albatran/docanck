import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';

export const SubCategoryStore = defineStore("SubCategoryData",{
    state : () => ({


      toast: useToast(),
      loadingSubCategory: false,
      subcategories: [],

      loadingSubmitSubCategory: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedSubCategorys: [],
      ids: null,


    //   data
      id: '',
      name: '',
      category_id: '',


        // paginate
      current_page: 1,

        //for selected when add or update recourd
      loadList: false,
      categoryList: [],

    //   mode
    isAddMode: false,
    dialogVisible: false,
    editMode: false,


          // filters
    filter_type: null,
    // for delete mode
    dialogDelete: false,

    // for delete select mode
    dialogDeleteSelect: false

    }),

    getters: {

    },

    actions: {

        async showSubCategorys(current_page){
           this.loadingSubCategory = true
           let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;

           http().get('api/sub-categories', {
                params: {
                    page: this.current_page,
                    search: this.search,
                    type: this.filter_type? this.filter_type.name : '',
                    per_page: this.per_page,
                    sort_field: this.sortField,
                    sort_order: this.sortOrder
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.subcategories = response.data.subCategory
                console.log(response.data.subCategory)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingSubCategory = false
            })
        },
        async AddSubCategory(){
           this.loadingSubmitSubCategory = true



           let form_data = new FormData();

            if (this.name !== null && this.name !== '') {
                form_data.append('name', this.name);
            }
           if(this.category_id !== null && this.category_id !== ''){

               form_data.append('category_id', this.category_id.id);
           }



           http().post('api/sub-categories', form_data, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
           })
           .then((response) => {
                console.log(response.data)
                //   ElNotification({
                //     title: 'success',
                //     message: response.data.message,
                //     type: 'success',
                //     showClose: false,
                // })
                this.toast.add({
                    severity: "success",
                    summary: "Successful",
                    detail: response.data.message,
                    life: 3000,
                });
                this.isAddMode = false;
                this.dialogVisible = false;
                this.editMode = false;
                this.showSubCategorys()
                this.resetFormData()

            })
            .catch((error) => {
                console.log(error)
                if(!error.response && error.message === 'Network Error'){
                    ElNotification({
                    title: 'Error',
                    message: 'تأكد من الاتصال بالانترنت',
                    type: 'error',
                    showClose: false,
                    })
                }else{

                    ElNotification({
                        title: 'Error',
                        message: error.response.data.message,
                        type: 'error',
                        showClose: false,
                    })
                }
            }).finally(() => {
                this.loadingSubmitSubCategory = false
            })
        },
        openaddmodel(){
            this.isAddMode = true;
            this.dialogVisible = true;
            this.editMode = false;
            this.resetFormData();
        },
        resetFormData(){
            this.id= null
            this.name= null
            this.category_id= null

        },
        openEditModel(product){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= product.id
            this.name= product.name
            this.category_id= product.category

        },
         async UpdateSubCategory(){
           this.loadingSubmitSubCategory = true



           let form_data = new FormData();

            if (this.name !== null && this.name !== '') {
                form_data.append('name', this.name);
            }
           if(this.category_id !== null && this.category_id !== ''){

               form_data.append('category_id', this.category_id.id);
           }
            form_data.append('_method', 'put');

           http().post('api/sub-categories/'+this.id, form_data, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
           })
           .then((response) => {
                console.log(response.data)
                  ElNotification({
                    title: 'success',
                    message: response.data.message,
                    type: 'success',
                    showClose: false,
                })
                this.isAddMode = false;
                this.dialogVisible = false;
                this.editMode = false;
                this.showSubCategorys()
                this.resetFormData()

            })
            .catch((error) => {
                console.log(error)
                if(!error.response && error.message === 'Network Error'){
                    ElNotification({
                    title: 'Error',
                    message: 'تأكد من الاتصال بالانترنت',
                    type: 'error',
                    showClose: false,
                    })
                }else{

                    ElNotification({
                        title: 'Error',
                        message: error.response.data.message,
                        type: 'error',
                        showClose: false,
                    })
                }
            }).finally(() => {
                this.loadingSubmitSubCategory = false
            })
        },
        opendeletemodel(sub_category){
            this.dialogDelete = true;

            this.id= sub_category.id
        },
        async deleteSubCategory(){
            this.loadingSubmitSubCategory = true
           http().delete('api/sub-categories/'+this.id, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
           })
           .then((response) => {
                console.log(response.data)
                  ElNotification({
                    title: 'success',
                    message: response.data.message,
                    type: 'success',
                    showClose: false,
                })
                this.dialogDelete = false;
                this.showSubCategorys()
                this.id = ''
            })
            .catch((error) => {
                console.log(error)
                if(!error.response && error.message === 'Network Error'){
                    ElNotification({
                    title: 'Error',
                    message: 'تأكد من الاتصال بالانترنت',
                    type: 'error',
                    showClose: false,
                    })
                }else{

                    ElNotification({
                        title: 'Error',
                        message: error.response.data.message,
                        type: 'error',
                        showClose: false,
                    })
                }
            }).finally(() => {
                this.loadingSubmitSubCategory = false
            })
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedSubCategorys.map(item => item.id);
        },
        async deleteSelected(){

            this.loadingSubmitSubCategory = true
            http().post('api/sub-categories/delete-multiple', {
                ids: this.ids
            }, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
           })
           .then((response) => {
                console.log(response.data)
                this.toast.add({
                    severity: "success",
                    summary: "Successful",
                    detail: response.data.message,
                    life: 3000,
                });
               this.dialogDeleteSelect = false;
               this.selectedSubCategorys = [];
               this.ids = null;
                this.showSubCategorys()

            })
            .catch((error) => {
                console.log(error)
                if(!error.response && error.message === 'Network Error'){
                    ElNotification({
                    title: 'Error',
                    message: 'تأكد من الاتصال بالانترنت',
                    type: 'error',
                    showClose: false,
                    })
                }else{

                    ElNotification({
                        title: 'Error',
                        message: error.response.data.message,
                        type: 'error',
                        showClose: false,
                    })
                }
            }).finally(() => {
                this.loadingSubmitSubCategory = false
            })




        },
         async getSelect(){
           this.loadList = true

           http().get('api/sub-categories/get-select', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.categoryList = response.data.categoryList
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadList = false
            })
        },

    }

})
