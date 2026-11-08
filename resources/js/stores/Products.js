import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';

export const ProductStore = defineStore("ProductData",{
    state : () => ({


      toast: useToast(),
      loadingProduct: false,
      products: [],

      loadingSubmitProduct: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedProducts: [],
      ids: null,


      src: '',
    //   data
      id: '',
      name: '',
      description: '',
      price: '',
      image: '',
      user_id: '',
      category_id: '',
      sub_category_id: '',
      type: '',
      count: '',


        // paginate
      current_page: 1,

        //for selected when add or update recourd
      loadList: false,
      categoryList: [],
      subCategoryList: [],
      storeList: [],

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

        async showProducts(current_page){
           this.loadingProduct = true
           let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;

           http().get('api/products', {
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
                this.products = response.data.products
                console.log(this.products)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingProduct = false
            })
        },
        onFileSelect(event) {
            const file = event.files[0];
            this.image = file;
            const reader = new FileReader();

            reader.onload = async (e) => {
                this.src = e.target.result;
            };

            reader.readAsDataURL(file);
        },
        async AddProduct(){
           this.loadingSubmitProduct = true



           let form_data = new FormData();

            if (this.name !== null && this.name !== '') {
                form_data.append('name', this.name);
            }
           if(this.description !== null && this.description !== ''){

               form_data.append('description', this.description);
           }
           if(this.price !== null && this.price !== ''){

               form_data.append('price', this.price);
           }
           if(this.type !== null && this.type !== ''){

               form_data.append('type', this.type);
           }
           if(this.count !== null && this.count !== ''){

               form_data.append('count', this.count);
           }
           if(this.user_id !== null && this.user_id !== ''){

               form_data.append('user_id', this.user_id.id);
           }
           if(this.category_id !== null && this.category_id !== ''){

               form_data.append('category_id', this.category_id.id);
           }
           if(this.sub_category_id !== null && this.sub_category_id !== ''){

               form_data.append('sub_category_id', this.sub_category_id.id);
           }
           if(this.image !== null && this.image !== ''){

               form_data.append('image', this.image);
           }


           http().post('api/products', form_data, {
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
                this.showProducts()
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
                this.loadingSubmitProduct = false
            })
        },
        openaddmodel(){
            this.isAddMode = true;
            this.dialogVisible = true;
            this.editMode = false;
            // this.src= null
            this.resetFormData();
        },
        resetFormData(){
            this.id= null
            this.name= null
            this.description= null
            this.price= null
            this.count= null
            this.type= null
            this.image= null
            this.src= null
            this.user_id= null
            this.category_id= null
            this.sub_category_id= null
            this.subCategoryList= null

        },
        openEditModel(product){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= product.id
            this.name= product.name
            this.description= product.description
            this.price= product.price
            this.count= product.count
            this.type= product.type
            this.image= product.image
            this.src= product.image
            this.user_id= product.user
            this.category_id= product.category
            this.sub_category_id= product.subcategory

        },
         async UpdateProduct(){
           this.loadingSubmitProduct = true



           let form_data = new FormData();

            if (this.name !== null && this.name !== '') {
                form_data.append('name', this.name);
            }
           if(this.description !== null && this.description !== ''){

               form_data.append('description', this.description);
           }
           if(this.price !== null && this.price !== ''){

               form_data.append('price', this.price);
           }
            if(this.type !== null && this.type !== ''){

               form_data.append('type', this.type);
           }
           if(this.count !== null && this.count !== ''){

               form_data.append('count', this.count);
           }
           if(this.user_id !== null && this.user_id !== ''){

               form_data.append('user_id', this.user_id.id);
           }
           if(this.category_id !== null && this.category_id !== ''){

               form_data.append('category_id', this.category_id.id);
           }
           if(this.sub_category_id !== null && this.sub_category_id !== ''){

               form_data.append('sub_category_id', this.sub_category_id.id);
           }
           if(this.image !== null && this.image !== ''){

               form_data.append('image', this.image);
           }

            form_data.append('_method', 'put');

           http().post('api/products/'+this.id, form_data, {
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
                this.showProducts()
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
                this.loadingSubmitProduct = false
            })
        },
        opendeletemodel(Product){
            this.dialogDelete = true;

            this.id= Product.id
        },
        async deleteProduct(){
            this.loadingSubmitProduct = true
           http().delete('api/products/'+this.id, {
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
                this.showProducts()
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
                this.loadingSubmitProduct = false
            })
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedProducts.map(item => item.id);
        },
        async deleteSelected(){

            this.loadingSubmitProduct = true
            http().post('api/products/delete-multiple', {
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
               this.selectedProducts = [];
               this.ids = null;
                this.showProducts()

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
                this.loadingSubmitProduct = false
            })




        },
         async getSelect(category_id = null){
            console.log(category_id)
           this.loadList = true

           http().get('api/products/get-select', {
                params: {
                    category_id: category_id?.id
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.categoryList = response.data.categoryList
                this.storeList = response.data.storeList
                this.subCategoryList = response.data.subCategories
                this.sub_category_id = ''
                // console.log(subCategories);
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadList = false
            })
        },

    }

})
