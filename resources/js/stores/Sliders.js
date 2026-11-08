import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';
export const SliderStore = defineStore("SliderData",{
    state : () => ({


      toast: useToast(),
      loadingSlider: false,
      sliders: [],

      loadingSubmitSlider: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedSliders: [],
      ids: null,


      src: '',
    //   data
      id: '',
      image: '',

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

        async showSliders(current_page){
           this.sliders = []
           this.loadingSlider = true
            let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;

           http().get('api/sliders', {
                params: {
                    page: this.current_page,
                    search: this.search,
                    per_page: this.per_page,
                    sort_field: this.sortField,
                    sort_order: this.sortOrder
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.sliders = response.data.sliders
                console.log(this.sliders)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingSlider = false
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
        async AddSlider(){
           this.loadingSubmitSlider = true



           let form_data = new FormData();


           if(this.image !== null && this.image !== ''){

               form_data.append('image', this.image);
           }


           http().post('api/sliders', form_data, {
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
                this.showSliders()
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
                this.loadingSubmitSlider = false
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
            this.image= null
            this.src= null

        },
        openEditModel(slider){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= slider.id
            this.image= slider.image
            this.src= slider.image

        },
         async UpdateSlider(){
           this.loadingSubmitSlider = true



           let form_data = new FormData();


           if(this.image !== null && this.image !== ''){

               form_data.append('image', this.image);
           }
            form_data.append('_method', 'put');

           http().post('api/sliders/'+this.id, form_data, {
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
                this.showSliders()
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
                this.loadingSubmitSlider = false
            })
        },
        opendeletemodel(slider){
            this.dialogDelete = true;

            this.id= slider.id
        },
        async deleteSlider(){
            this.loadingSubmitSlider = true
           http().delete('api/sliders/'+this.id, {
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
                this.showSliders()
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
                this.loadingSubmitSlider = false
            })
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedSliders.map(item => item.id);
        },
        async deleteSelected(){

            this.loadingSubmitSlider = true
            http().post('api/sliders/delete-multiple', {
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
               this.selectedSliders = [];
               this.ids = null;
                this.showSliders()

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
                this.loadingSubmitSlider = false
            })




        }

    }

})
