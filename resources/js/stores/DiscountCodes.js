import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import { useToast } from "primevue/usetoast";
import http from '../helpers/http';

export const DiscountCodeStore = defineStore("DiscountCodeData",{
    state : () => ({


      toast: useToast(),
      loadingDiscountCode: false,
      discountcodes: [],

      loadingSubmitDiscountCode: false,

      //   search
      search: null,
      sortField: null,
      sortOrder : null,
      per_page: 4,

    //   for delete multi colom
      selectedDiscountCodes: [],
      ids: null,


    //   data
      id: '',
      code: '',
      Percentage: '',


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

        async showDiscountCodes(current_page){
           this.loadingDiscountCode = true
           let currentPage = current_page ?? this.current_page;
            this.current_page = currentPage;

            try {
                const response = await http().get('api/discount-codes', {
                    params: {
                        page: this.current_page,
                        search: this.search,
                        per_page: this.per_page,
                        sort_field: this.sortField,
                        sort_order: this.sortOrder
                    }
                });
                this.discountcodes = response.data.discountcodes;
            } catch (error) {
                // الخطأ سيظهر تلقائياً عبر الـ Interceptor
            } finally {
                this.loadingDiscountCode = false;
            }

        },
        async AddDiscountCode(){
           this.loadingSubmitDiscountCode = true
           const formData = this.prepareFormData();
           try {
                const response = await http().post('api/discount-codes', formData);

                this.toast.add({
                    severity: "success",
                    summary: "تمت العملية",
                    detail: response.data.message,
                    life: 3000,
                });

                this.closeDialogs();
                await this.showDiscountCodes();
                this.resetFormData();
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitDiscountCode = false;
            }

        },
        async UpdateDiscountCode(){
           this.loadingSubmitDiscountCode = true
            const formData = this.prepareFormData();
            formData.append('_method', 'put');

            try {
                const response = await http().post(`api/discount-codes/${this.id}`, formData);

                ElNotification({
                    title: 'نجاح',
                    message: response.data.message,
                    type: 'success',
                });

                this.closeDialogs();
                await this.showDiscountCodes();
                this.resetFormData();
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitDiscountCode = false;
            }
        },
        async deleteDiscountCode(){
            this.loadingSubmitDiscountCode = true
            try {
                const response = await http().delete(`api/discount-codes/${this.id}`);

                ElNotification({ title: 'نجاح', message: response.data.message, type: 'success' });

                this.dialogDelete = false;
                await this.showDiscountCodes();
                this.id = '';
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitDiscountCode = false;
            }
        },
        async deleteSelected(){

            this.loadingSubmitDiscountCode = true
                        try {
                const response = await http().post('api/discount-codes/delete-multiple', { ids: this.ids });

                this.toast.add({
                    severity: "success",
                    summary: "تم الحذف",
                    detail: response.data.message,
                    life: 3000,
                });

                this.dialogDeleteSelect = false;
                this.selectedDiscountCodes = [];
                this.ids = null;
                await this.showDiscountCodes();
            } catch (error) {
                // المعالجة في الـ Interceptor
            } finally {
                this.loadingSubmitDiscountCode = false;
            }
        },
         async getSelect(){
           this.loadList = true
             try {
                const response = await http().get('api/discount-codes/get-select', {

                });
                this.categoryList = response.data.categoryList
            } catch (error) {
                // الخطأ سيظهر تلقائياً عبر الـ Interceptor
            } finally {
                this.loadList = false;
            }

        //    http().get('api/discount-codes/get-select', {
        //         headers: {
        //             Authorization: `Bearer ${localStorage.getItem('token')}`
        //         }
        //    })
        //    .then((response) => {
        //         this.categoryList = response.data.categoryList
        //     })
        //     .catch((error) => {
        //         console.log(error)
        //     }).finally(() => {
        //         this.loadList = false
        //     })
        },
         // --- Helper Functions (دوال مساعدة لتنظيف الكود) ---
        prepareFormData() {
            let form_data = new FormData();
            if (this.code) form_data.append('code', this.code);
            if (this.Percentage) form_data.append('Percentage', this.Percentage);
            return form_data;
        },
        closeDialogs() {
            this.isAddMode = false;
            this.dialogVisible = false;
            this.editMode = false;
        },
        openaddmodel(){
            this.resetFormData();
            this.isAddMode = true;
            this.dialogVisible = true;
            this.editMode = false;
        },
        resetFormData(){
            this.id= null
            this.code= null
            this.Percentage= null

        },
        openEditModel(discountCode){

            this.editMode = true;
            this.dialogVisible = true;
            this.isAddMode = false;

            this.id= discountCode.id
            this.code= discountCode.code
            this.Percentage= discountCode.Percentage

        },
        opendeletemodel(discountcode){
            this.dialogDelete = true;

            this.id= discountcode.id
        },
        opendeleteSelect(){
            this.dialogDeleteSelect = true;

            this.ids = this.selectedDiscountCodes.map(item => item.id);
        },

    }

})
