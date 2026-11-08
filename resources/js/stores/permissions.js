import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import http from '../helpers/http'

export const PermissionStore = defineStore("PermissionData",{
    state : () => ({
      loadingPermission: false,
      Permissions: [],

      loadingSubmitPermission: false,

      //   search
      search: null,



    //   data
      id: '',
      name: '',
      guard_name: '',


    // paginate
      current_page: 1,

    //   mode
    isAddMode: false,
    dialogVisible: false,
    editMode: false,


    // for delete mode
    dialogDelete: false

    }),

    getters: {

    },

    actions: {

        async showPermission(current_page){
           this.Permissions = []
           this.loadingPermission = true
            let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;
           http().get('api/permissions', {
                params: {
                    page: currentPage,
                    search: this.search
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.Permissions = response.data.permissions
                console.log(this.Permissions)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingPermission = false
            })
        },
        async AddPermission(){
           this.loadingSubmitPermission = true



           let form_data = new FormData();

            form_data.append('name', this.name);
            form_data.append('guard_name', this.guard_name.valuee);

           http().post('api/permissions', form_data, {
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
                this.showPermission()
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
                this.loadingSubmitPermission = false
            })
        },
        openaddmodel(){
            this.isAddMode = true;
            this.dialogVisible = true;
            this.editMode = false;
            // this.resetFormData();
        },
        resetFormData(){
            this.id= null
            this.name= null
            this.guard_name= null

        },
        openEditModel(permission){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= permission.id
            this.name= permission.name
            this.guard_name= permission.guard_name
        },
         async UpdatePermission(){
           this.loadingSubmitPermission = true

           http().put('api/permissions/'+this.id, {
            name: this.name,
            guard_name: this.guard_name.valuee,
           }, {
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
                this.showPermission()
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
                this.loadingSubmitPermission = false
            })
        },
        opendeletemodel(permission){
            this.dialogDelete = true;

            this.id= permission.id
        },
        async deletePermission(){
           http().delete('api/permissions/'+this.id, {
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
                this.showPermission()

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

            })
        }



    }

})
