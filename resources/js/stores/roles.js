import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import http from '../helpers/http'

export const RoleStore = defineStore("RoleData",{
    state : () => ({
      loadingRole: false,
      roles: [],

      loadingSubmitRole: false,
      loadList: false,
      userList: [],

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

        async showRole(current_page){
           this.roles = []
           this.loadingRole = true
             let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;
           http().get('api/roles', {
                params: {
                    page: this.current_page,
                    search: this.search
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.roles = response.data.roles
                console.log(this.roles)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingRole = false
            })
        },
        async AddRole(){
           this.loadingSubmitRole = true



           let form_data = new FormData();

            form_data.append('name', this.name);
            form_data.append('guard_name', this.guard_name.valuee);

           http().post('api/roles', form_data, {
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
                this.showRole()
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
                this.loadingSubmitRole = false
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
        openEditModel(role){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= role.id
            this.name= role.name
            this.guard_name= role.guard_name
        },
         async UpdateRole(){
           this.loadingSubmitRole = true

           http().put('api/roles/'+this.id, {
            name: this.name,
            guard_name: this.guard_name,
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
                this.showRole()
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
                this.loadingSubmitRole = false
            })
        },
        opendeletemodel(role){
            this.dialogDelete = true;

            this.id= role.id
        },
        async deleteRole(){
           http().delete('api/roles/'+this.id, {
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
                this.showRole()

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
        },
        async getSelect(){

            this.userList = []
           this.loadList = true
           http().get('api/organization/observer', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.userList = response.data.users
                console.log(this.userList)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadList = false
            })
        }



    }

})
