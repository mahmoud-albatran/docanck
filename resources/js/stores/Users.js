import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';

export const UserStore = defineStore("UserData",{
    state : () => ({


      toast: useToast(),
      loadingUser: false,
      users: [],

      loadingSubmitUser: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedUsers: [],
      ids: null,


      src: '',
    //   data
      id: '',
      name: '',
      email: '',
      password: '',
      profile_photo_path: '',
      phone_number: '',
      store_name: '',
      category_id: '',
      description: '',
      location: '',
      location_desc: '',
    //   for dashboard users
      role_id: '',


    //   import
      loadImport: false,
    file: null,
    storeIdFile: '',

        // paginate
      current_page: 1,
      role_type: 'client',

        //for selected when add or update recourd
      loadList: false,
      categoryList: [],
      roleList: [],



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
        async showUsers(current_page, role_type){
           this.loadingUser = true
           let currentPage = current_page ?? this.current_page;
           let roleType = role_type ?? this.role_type;
            this.current_page = currentPage;
            this.role_type = roleType;

           http().get('api/users', {
                params: {
                    page: this.current_page,
                    roleType: this.role_type,
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
                this.users = response.data.users
                console.log(this.users)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingUser = false
            })
        },
        onFileSelect(event) {
            const file = event.files[0];
            this.profile_photo_path = file;
            const reader = new FileReader();

            reader.onload = async (e) => {
                this.src = e.target.result;
            };

            reader.readAsDataURL(file);
        },
        async AddUser(){
           this.loadingSubmitUser = true



           let form_data = new FormData();

            if (this.name !== null && this.name !== '') {
                form_data.append('name', this.name);
            }
           if(this.email !== null && this.email !== ''){

               form_data.append('email', this.email);
           }
           if(this.password !== null && this.password !== ''){

               form_data.append('password', this.password);
           }
           if(this.phone_number !== null && this.phone_number !== ''){

               form_data.append('phone_number', this.phone_number);
           }

           if(this.profile_photo_path !== null && this.profile_photo_path !== ''){

               form_data.append('profile_photo_path', this.profile_photo_path);
           }
           if(this.store_name !== null && this.store_name !== ''){

               form_data.append('store_name', this.store_name);
           }
           if(this.category_id !== null && this.category_id !== ''){

               form_data.append('category_id', this.category_id.id);
           }
           if(this.description !== null && this.description !== ''){

               form_data.append('description', this.description);
           }
           if(this.location !== null && this.location !== ''){

               form_data.append('location', this.location);
           }
           if(this.location_desc !== null && this.location_desc !== ''){

               form_data.append('location_desc', this.location_desc);
           }

           if(this.role_id !== null && this.role_id !== ''){

               form_data.append('role_id', this.role_id.id);
           }


           http().post('api/users', form_data, {
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
                this.showUsers()
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
                this.loadingSubmitUser = false
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
            this.email= null
            this.password= null
            this.profile_photo_path= null
            this.src= null
            this.phone_number= null
            this.store_name= null
            this.category_id= null
            this.description= null
            this.location= null
            this.location_desc= null
            this.role_id= null

        },
        openEditModel(user){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= user.id
            this.name= user.name
            this.email= user.email
            this.password= user.password
            this.profile_photo_path= user.profile_photo_path
            this.src= user.profile_photo_path
            this.phone_number= user.phone_number
            this.store_name= user.store_name
            this.category_id = !user.category || (Array.isArray(user.category) ? user.category.length === 0 : Object.keys(user.category).length === 0) ? null : user.category
            this.description= user.description
            this.location= user.location
            this.location_desc= user.location_desc
            this.role_id = user.role && user.role.length ? user.role[0] : null

        },
         async UpdateUser(){
           this.loadingSubmitUser = true



           let form_data = new FormData();

            if (this.name !== null && this.name !== '') {
                form_data.append('name', this.name);
            }
           if(this.email !== null && this.email !== ''){

               form_data.append('email', this.email);
           }
           if(this.password !== null && this.password !== ''){

               form_data.append('password', this.password);
           }
           if(this.phone_number !== null && this.phone_number !== ''){

               form_data.append('phone_number', this.phone_number);
           }

           if(this.profile_photo_path !== null && this.profile_photo_path !== ''){

               form_data.append('profile_photo_path', this.profile_photo_path);
           }
            if(this.store_name !== null && this.store_name !== ''){

               form_data.append('store_name', this.store_name);
           }
           if(this.category_id !== null && this.category_id !== ''){

               form_data.append('category_id', this.category_id.id);
           }
           if(this.description !== null && this.description !== ''){

               form_data.append('description', this.description);
           }
           if(this.location !== null && this.location !== ''){

               form_data.append('location', this.location);
           }
           if(this.location_desc !== null && this.location_desc !== ''){

               form_data.append('location_desc', this.location_desc);
           }

            if(this.role_id !== null && this.role_id !== ''){

               form_data.append('role_id', this.role_id.id);
           }

            form_data.append('_method', 'put');

           http().post('api/users/'+this.id, form_data, {
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
                this.showUsers()
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
                this.loadingSubmitUser = false
            })
        },
        opendeletemodel(User){
            this.dialogDelete = true;

            this.id= User.id
        },
        async deleteUser(){
            this.loadingSubmitUser = true
           http().delete('api/users/'+this.id, {
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
                this.showUsers()
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
                this.loadingSubmitUser = false
            })
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedUsers.map(item => item.id);
        },
        async deleteSelected(){

            this.loadingSubmitUser = true
            http().post('api/users/delete-multiple', {
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
               this.selectedUsers = [];
               this.ids = null;
                this.showUsers()

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
                this.loadingSubmitUser = false
            })




        },
         async getSelect(){
           this.loadList = true

           http().get('api/users/get-select', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.categoryList = response.data.categoryList
                this.roleList = response.data.roleList
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadList = false
            })
        },
        async importExcel(){
            this.loadImport = true
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('storeIdFile', this.storeIdFile);
            // formData.append('date', date);

            http().post('api/users/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            }).then((response) => {
                 ElNotification({
                    title: 'success',
                    message: response.data.message,
                    type: 'success',
                    showClose: false,
                })
            })
            .catch((error) => {
                console.log(error)
                   ElNotification({
                        title: 'Error',
                        message: error.response.data.message,
                        type: 'error',
                        showClose: false,
                    })
            }).finally(() => {
                this.loadImport = false
            })

        },

    }

})
