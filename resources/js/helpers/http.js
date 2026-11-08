import axios from 'axios'
import { ElNotification } from 'element-plus'
const http = () => {
    let options = {
        baseURL:'https://docank.mahmoudalbatran.com/',
        headers: {}
    }
    if(localStorage.getItem('token')){
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    }

    const instance = axios.create(options);



    // --- توحيد معالجة الأخطاء ---
    instance.interceptors.response.use(
        (response) => response, // إذا الاستجابة ناجحة مررها كما هي
        (error) => {
            console.log("Axios Global Error:", error);

            // 1. خطأ في الاتصال (Network Error)
            if (!error.response) {
                ElNotification({
                    title: 'خطأ في الاتصال',
                    message: 'تأكد من الاتصال بالانترنت',
                    type: 'error',
                });
            }
            // 2. أخطاء قادمة من السيرفر (4xx, 5xx)
            else {
                const status = error.response.status;
                const message = error.response.data.message || 'حدث خطأ غير متوقع';

                if (status === 401) {
                    localStorage.removeItem('token');
                    // اختياري: window.location.href = '/'; // توجيه لتسجيل الدخول
                }

                // عرض التنبيه تلقائياً لأي خطأ
                ElNotification({
                    title: 'خطأ',
                    message: message,
                    type: 'error',
                });
            }

            return Promise.reject(error); // تمرير الخطأ لمن يريد معالجته محلياً أيضاً
        }
    );







    return instance;



    // return  axios.create(options)
}


export default http









