import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';

export const OrderStore = defineStore("OrderData",{
    state : () => ({


      toast: useToast(),
      loadingOrder: false,
      orders: [],

      loadingSubmitOrder: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedOrders: [],
      ids: null,


    //   data
      id: '',
      status: '',
      message: '',


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

        async showOrders(current_page){
           this.loadingOrder = true
           let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;

           http().get('api/orders', {
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
                this.orders = response.data.orders
                console.log(response.data.orders)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingOrder = false
            })
        },
        async AddOrder(){
           this.loadingSubmitOrder = true



           let form_data = new FormData();

            if (this.status !== null && this.status !== '') {
                form_data.append('status', this.status);
            }
           if(this.message !== null && this.message !== ''){

               form_data.append('message', this.message);
           }



           http().post('api/orders', form_data, {
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
                this.showOrders()
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
                this.loadingSubmitOrder = false
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
            this.message= null

        },
        openEditModel(product){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= product.id
            this.name= product.name
            this.category_id= product.category

        },
         async UpdateOrder(){
           this.loadingSubmitOrder = true

            console.log(this.status.valuee)

           let form_data = new FormData();

            if (this.status !== null && this.status !== '') {
                form_data.append('status', this.status.valuee);
            }
           if(this.message !== null && this.message !== ''){

               form_data.append('message', this.message);
           }
            form_data.append('_method', 'put');

           http().post('api/orders/'+this.id, form_data, {
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
                this.showOrders()
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
                this.loadingSubmitOrder = false
            })
        },
        opendeletemodel(sub_category){
            this.dialogDelete = true;

            this.id= sub_category.id
        },
        async deleteOrder(){
            this.loadingSubmitOrder = true
           http().delete('api/orders/'+this.id, {
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
                this.showOrders()
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
                this.loadingSubmitOrder = false
            })
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedOrders.map(item => item.id);
        },
        async deleteSelected(){

            this.loadingSubmitOrder = true
            http().post('api/orders/delete-multiple', {
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
               this.selectedOrders = [];
               this.ids = null;
                this.showOrders()

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
                this.loadingSubmitOrder = false
            })




        },
         async getSelect(){
           this.loadList = true

           http().get('api/orders/get-select', {
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
