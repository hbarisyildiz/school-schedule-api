// ============================================
// Modern Admin Panel JavaScript
// School Schedule Management System
// ============================================

const API_BASE_URL = 'http://localhost/api';

const { createApp } = Vue;

createApp({
    data() {
        return {
            // App State
            activeTab: 'dashboard',
            user: null,
            showLogin: true,
            isLoggingIn: false,
            
            // Login Form
            loginForm: {
                email: '',
                password: ''
            },
            loginError: '',
            
            // Messages
            message: '',
            error: '',
            modalError: '',
            
            // School Settings
            schoolSettings: {
                class_days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                lesson_duration: 40,
                break_durations: {
                    small_break: 10,
                    lunch_break: 20
                },
                school_hours: {
                    start_time: '08:00',
                    end_time: '16:00'
                },
                weekly_lesson_count: 30,
                schedule_settings: {
                    allow_teacher_conflicts: false,
                    allow_classroom_conflicts: false,
                    max_lessons_per_day: 8,
                    min_lessons_per_day: 4
                },
                daily_lesson_counts: {
                    monday: 8,
                    tuesday: 8,
                    wednesday: 8,
                    thursday: 8,
                    friday: 8
                },
                class_daily_lesson_counts: {},
                teacher_daily_lesson_counts: {}
            },
            selectedClassForDailyCount: '',
            schoolSettingsLoading: false,
            isSavingSettings: false,
            weekDays: [
                { value: 'monday', label: 'Pazartesi' },
                { value: 'tuesday', label: 'Salı' },
                { value: 'wednesday', label: 'Çarşamba' },
                { value: 'thursday', label: 'Perşembe' },
                { value: 'friday', label: 'Cuma' },
                { value: 'saturday', label: 'Cumartesi' },
                { value: 'sunday', label: 'Pazar' }
            ],
            
            // Dashboard Stats
            stats: {
                totalSchools: 0,
                totalUsers: 0,
                totalClasses: 0,
                totalSubjects: 0
            },
            
            // Schools
            schools: [],
            schoolsLoading: false,
            editSchoolModal: false,
            editSchoolData: {},
            viewSchoolModal: false,
            selectedSchool: null,
            addSchoolModal: false,
            isAddingSchool: false,
            isLoadingDistricts: false,
            schoolModalError: '',
            newSchool: {
                name: '',
                email: '',
                password: '',
                phone: '',
                city_id: '',
                district_id: '',
                website: '',
                subscription_plan_id: ''
            },
            cities: [],
            districts: [],
            subscriptionPlans: [],
            
            // Users
            users: [],
            usersLoading: false,
            isUploadingExcel: false,
            editUserModal: false,
            editUserData: {},
            addUserModal: false,
            newUser: {
                name: '',
                short_name: '',
                branch: '',
                email: '',
                password: '',
                password_confirmation: '',
                role_id: '',
                phone: '',
                address: ''
            },
            roles: [],
            
            // Subjects
            subjects: [],
            subjectsLoading: false,
            editSubjectModal: false,
            editSubjectData: {},
            addSubjectModal: false,
            newSubject: {
                name: '',
                code: '',
                weekly_hours: 4,
                description: ''
            },
            
            // Classes
            classes: [],
            classesLoading: false,
            addClassModal: false,
            editClassModal: false,
            newClass: {
                name: '',
                grade: '',
                branch: '',
                class_teacher_id: '',
                description: ''
            },
            
            // Class Schedule Modal
            classScheduleModal: false,
            selectedClassForSchedule: null,
            classScheduleData: {},
            
            // Teacher Schedule Modal
            teacherScheduleModal: false,
            selectedTeacherForSchedule: null,
            teacherScheduleData: {},
            
            editClassData: {},
            teachers: [],
            
            // Schedules
            schedules: [],
            schedulesLoading: false,
            editScheduleModal: false,
            editScheduleData: {},
            
            // Search & Filter
            searchQuery: '',
            filterStatus: 'all',
            
            // Pagination
            currentPage: 1,
            totalPages: 1,
            perPage: 15
        }
    },
    
    computed: {
        filteredSchools() {
            let filtered = this.schools;
            
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(school => 
                    school.name.toLowerCase().includes(query) ||
                    school.email.toLowerCase().includes(query)
                );
            }
            
            if (this.filterStatus !== 'all') {
                filtered = filtered.filter(school => 
                    school.subscription_status === this.filterStatus
                );
            }
            
            return filtered;
        },
        
        filteredUsers() {
            if (!this.searchQuery) return this.users;
            
            const query = this.searchQuery.toLowerCase();
            return this.users.filter(user => 
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query)
            );
        }
    },
    
    mounted() {
        // Check if user is already logged in (token in localStorage)
        const token = localStorage.getItem('auth_token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            this.loadUser();
        }
    },
    
    methods: {
        // ===== Authentication =====
        async login() {
            this.isLoggingIn = true;
            this.loginError = '';
            
            try {
                const response = await axios.post(`${API_BASE_URL}/auth/login`, this.loginForm);
                
                // Save token
                localStorage.setItem('auth_token', response.data.access_token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
                
                // Set user
                this.user = response.data.user;
                this.showLogin = false;
                this.message = `Hoş geldiniz, ${this.user.name}!`;
                
                // Load initial data
                this.loadDashboard();
                
            } catch (error) {
                this.loginError = error.response?.data?.message || 'Giriş başarısız. Lütfen bilgilerinizi kontrol edin.';
            } finally {
                this.isLoggingIn = false;
            }
        },
        
        async quickLogin(email) {
            const passwords = {
                'admin@schoolschedule.com': 'admin123',
                'mudur@ataturklisesi.edu.tr': 'mudur123',
                'ayse.yilmaz@ataturklisesi.edu.tr': 'teacher123'
            };
            
            this.loginForm.email = email;
            this.loginForm.password = passwords[email];
            await this.login();
        },
        
        async loadUser() {
            try {
                const response = await axios.get(`${API_BASE_URL}/user`);
                this.user = response.data;
                this.showLogin = false;
                this.loadDashboard();
            } catch (error) {
                localStorage.removeItem('auth_token');
                delete axios.defaults.headers.common['Authorization'];
                this.showLogin = true;
            }
        },
        
        logout() {
            localStorage.removeItem('auth_token');
            delete axios.defaults.headers.common['Authorization'];
            this.user = null;
            this.showLogin = true;
            this.activeTab = 'dashboard';
            this.message = 'Başarıyla çıkış yaptınız.';
        },
        
        // ===== Dashboard =====
        async loadDashboard() {
            try {
                // Load real statistics from API
                const response = await axios.get(`${API_BASE_URL}/dashboard/stats`);
                
                const statsData = response.data.stats;
                
                // Super Admin stats
                if (response.data.user_type === 'super_admin') {
                    this.stats = {
                        totalSchools: statsData.total_schools || 0,
                        activeSchools: statsData.active_schools || 0,
                        totalUsers: statsData.total_users || 0,
                        totalTeachers: statsData.total_teachers || 0,
                        totalStudents: statsData.total_students || 0,
                        totalClasses: statsData.total_classes || 0,
                        totalSubjects: statsData.total_subjects || 0,
                        totalSchedules: statsData.total_schedules || 0,
                        activeSubscriptions: statsData.active_subscriptions || 0,
                        expiredSubscriptions: statsData.expired_subscriptions || 0,
                        trialEndingSoon: statsData.trial_ending_soon || 0,
                        schoolsThisMonth: statsData.schools_this_month || 0,
                        activeToday: statsData.active_today || 0
                    };
                } else {
                    // School Admin stats
                    this.stats = {
                        schoolName: statsData.school_name,
                        totalTeachers: statsData.total_teachers || 0,
                        totalStudents: statsData.total_students || 0,
                        totalClasses: statsData.total_classes || 0,
                        totalSubjects: statsData.total_subjects || 0,
                        totalSchedules: statsData.total_schedules || 0,
                        subscriptionPlan: statsData.subscription_plan,
                        subscriptionEndsAt: statsData.subscription_ends_at,
                        daysLeft: statsData.days_left,
                        unreadNotifications: statsData.unread_notifications || 0
                    };
                }
                
            } catch (error) {
                console.error('Dashboard load error:', error);
                this.error = 'İstatistikler yüklenemedi';
            }
        },
        
        // ===== Schools Management =====
        async loadSchools() {
            this.schoolsLoading = true;
            try {
                const response = await axios.get(`${API_BASE_URL}/schools?page=${this.currentPage}`);
                
                if (response.data.data) {
                    this.schools = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.totalPages = response.data.last_page;
                } else {
                    this.schools = response.data;
                }
                
            } catch (error) {
                this.error = 'Okullar yüklenemedi';
                console.error('Schools load error:', error);
            } finally {
                this.schoolsLoading = false;
            }
        },
        
        editSchool(school) {
            this.editSchoolData = { ...school };
            this.editSchoolModal = true;
        },
        
        async updateSchool() {
            try {
                await axios.put(`${API_BASE_URL}/schools/${this.editSchoolData.id}`, this.editSchoolData);
                this.message = 'Okul başarıyla güncellendi';
                this.editSchoolModal = false;
                this.loadSchools();
            } catch (error) {
                this.error = error.response?.data?.message || 'Okul güncellenemedi';
            }
        },
        
        viewSchool(school) {
            this.selectedSchool = school;
            this.viewSchoolModal = true;
        },
        
        async openAddSchoolModal() {
            this.schoolModalError = '';
            this.districts = [];
            this.isLoadingDistricts = false;
            
            await this.loadCities();
            await this.loadSubscriptionPlans();
            this.addSchoolModal = true;
        },
        
        async loadCities() {
            try {
                const response = await axios.get(`${API_BASE_URL}/cities`);
                this.cities = response.data;
            } catch (error) {
                console.error('Cities load error:', error);
            }
        },
        
        async loadDistrictsForCity() {
            if (!this.newSchool.city_id) {
                this.districts = [];
                this.isLoadingDistricts = false;
                return;
            }
            
            this.isLoadingDistricts = true;
            this.newSchool.district_id = ''; // Reset district selection
            this.schoolModalError = '';
            
            try {
                const response = await axios.get(`${API_BASE_URL}/cities/${this.newSchool.city_id}/districts`);
                this.districts = response.data;
            } catch (error) {
                console.error('Districts load error:', error);
                this.schoolModalError = 'İlçeler yüklenirken hata oluştu';
            } finally {
                this.isLoadingDistricts = false;
            }
        },
        
        async loadSubscriptionPlans() {
            try {
                const response = await axios.get(`${API_BASE_URL}/subscription-plans`);
                this.subscriptionPlans = response.data;
            } catch (error) {
                console.error('Subscription plans load error:', error);
            }
        },
        
        async addSchool() {
            // Clear previous errors
            this.schoolModalError = '';
            
            // Validation
            if (!this.newSchool.name || !this.newSchool.email || !this.newSchool.password || 
                !this.newSchool.city_id || !this.newSchool.district_id || !this.newSchool.subscription_plan_id) {
                this.schoolModalError = '❌ Lütfen tüm zorunlu (*) alanları doldurun!';
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.newSchool.email)) {
                this.schoolModalError = '❌ Geçerli bir email adresi giriniz!';
                return;
            }
            
            // Password validation
            if (this.newSchool.password.length < 6) {
                this.schoolModalError = '❌ Şifre en az 6 karakter olmalıdır!';
                return;
            }
            
            this.isAddingSchool = true;
            
            try {
                const response = await axios.post(`${API_BASE_URL}/schools`, this.newSchool);
                
                this.message = `✅ ${this.newSchool.name} başarıyla oluşturuldu! Okul yöneticisi: ${response.data.admin.email}`;
                this.addSchoolModal = false;
                this.schoolModalError = '';
                
                // Reset form
                this.newSchool = {
                    name: '',
                    email: '',
                    password: '',
                    phone: '',
                    city_id: '',
                    district_id: '',
                    website: '',
                    subscription_plan_id: ''
                };
                this.districts = [];
                
                // Reload schools list
                this.loadSchools();
                
            } catch (error) {
                console.error('Add school error:', error);
                
                // Detaylı hata mesajı göster
                if (error.response?.data?.errors) {
                    // Validation errors
                    const errors = Object.values(error.response.data.errors).flat();
                    this.schoolModalError = '❌ ' + errors.join(', ');
                } else if (error.response?.data?.message) {
                    this.schoolModalError = '❌ ' + error.response.data.message;
                } else {
                    this.schoolModalError = '❌ Okul oluşturulurken hata oluştu. Lütfen tekrar deneyin.';
                }
            } finally {
                this.isAddingSchool = false;
            }
        },
        
        async deleteSchool(school) {
            if (!confirm(`${school.name} okulunu silmek istediğinizden emin misiniz?`)) {
                return;
            }
            
            try {
                await axios.delete(`${API_BASE_URL}/schools/${school.id}`);
                this.message = 'Okul başarıyla silindi';
                this.loadSchools();
            } catch (error) {
                this.error = error.response?.data?.message || 'Okul silinemedi';
            }
        },
        
        async toggleSchoolStatus(school) {
            try {
                await axios.post(`${API_BASE_URL}/schools/${school.id}/toggle-status`);
                this.message = `${school.name} durumu değiştirildi`;
                this.loadSchools();
            } catch (error) {
                this.error = 'Okul durumu değiştirilemedi';
            }
        },
        
        // ===== Users Management =====
        async loadUsers() {
            this.usersLoading = true;
            try {
                const response = await axios.get(`${API_BASE_URL}/users?page=${this.currentPage}`);
                
                if (response.data.data) {
                    this.users = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.totalPages = response.data.last_page;
                } else {
                    this.users = response.data;
                }
                
            } catch (error) {
                this.error = 'Kullanıcılar yüklenemedi';
                console.error('Users load error:', error);
            } finally {
                this.usersLoading = false;
            }
        },
        
        async loadRoles() {
            try {
                const response = await axios.get(`${API_BASE_URL}/roles`);
                this.roles = response.data;
            } catch (error) {
                console.error('Roles load error:', error);
            }
        },
        
        openAddUserModal() {
            this.modalError = '';
            this.loadRoles();
            this.addUserModal = true;
        },
        
        async addUser() {
            this.modalError = '';
            try {
                // Eğer öğretmen ise ve kısa ad boşsa, otomatik oluştur
                if (this.isTeacherRole(this.newUser.role_id) && !this.newUser.short_name) {
                    this.generateShortName();
                }
                
                await axios.post(`${API_BASE_URL}/users`, this.newUser);
                const userType = this.user.role?.name === 'super_admin' ? 'Kullanıcı' : 'Öğretmen';
                this.message = `${userType} başarıyla eklendi`;
                this.addUserModal = false;
                this.newUser = {
                    name: '',
                    short_name: '',
                    branch: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role_id: '',
                    phone: '',
                    address: ''
                };
                this.loadUsers();
            } catch (error) {
                this.modalError = error.response?.data?.message || 'Kullanıcı eklenemedi';
                console.error('Add user error:', error.response?.data);
            }
        },
        
        editUser(user) {
            this.modalError = '';
            this.editUserData = { ...user };
            this.editUserModal = true;
        },
        
        async updateUser() {
            this.modalError = '';
            try {
                await axios.put(`${API_BASE_URL}/users/${this.editUserData.id}`, this.editUserData);
                const userType = this.user.role?.name === 'super_admin' ? 'Kullanıcı' : 'Öğretmen';
                this.message = `${userType} başarıyla güncellendi`;
                this.editUserModal = false;
                this.loadUsers();
            } catch (error) {
                this.modalError = error.response?.data?.message || 'Kullanıcı güncellenemedi';
            }
        },
        
        async deleteUser(user) {
            if (!confirm(`${user.name} kullanıcısını silmek istediğinizden emin misiniz?`)) {
                return;
            }
            
            try {
                await axios.delete(`${API_BASE_URL}/users/${user.id}`);
                const userType = this.user.role?.name === 'super_admin' ? 'Kullanıcı' : 'Öğretmen';
                this.message = `${userType} başarıyla silindi`;
                this.loadUsers();
            } catch (error) {
                this.error = error.response?.data?.message || 'Kullanıcı silinemedi';
            }
        },
        
        async toggleUserStatus(user) {
            try {
                await axios.post(`${API_BASE_URL}/users/${user.id}/toggle-status`);
                this.message = `${user.name} durumu değiştirildi`;
                this.loadUsers();
            } catch (error) {
                this.error = 'Kullanıcı durumu değiştirilemedi';
            }
        },
        
        // ===== Subjects Management =====
        async loadSubjects() {
            this.subjectsLoading = true;
            try {
                const response = await axios.get(`${API_BASE_URL}/subjects`);
                
                if (response.data.data) {
                    this.subjects = response.data.data;
                } else {
                    this.subjects = response.data;
                }
                
            } catch (error) {
                this.error = 'Dersler yüklenemedi';
                console.error('Subjects load error:', error);
            } finally {
                this.subjectsLoading = false;
            }
        },
        
        openAddSubjectModal() {
            this.addSubjectModal = true;
        },
        
        async addSubject() {
            try {
                await axios.post(`${API_BASE_URL}/subjects`, this.newSubject);
                this.message = 'Ders başarıyla eklendi';
                this.addSubjectModal = false;
                this.newSubject = {
                    name: '',
                    code: '',
                    weekly_hours: 4,
                    description: ''
                };
                this.loadSubjects();
            } catch (error) {
                this.error = error.response?.data?.message || 'Ders eklenemedi';
            }
        },
        
        editSubject(subject) {
            this.editSubjectData = { ...subject };
            this.editSubjectModal = true;
        },
        
        async updateSubject() {
            try {
                await axios.put(`${API_BASE_URL}/subjects/${this.editSubjectData.id}`, this.editSubjectData);
                this.message = 'Ders başarıyla güncellendi';
                this.editSubjectModal = false;
                this.loadSubjects();
            } catch (error) {
                this.error = error.response?.data?.message || 'Ders güncellenemedi';
            }
        },
        
        async deleteSubject(subject) {
            if (!confirm(`${subject.name} dersini silmek istediğinizden emin misiniz?`)) {
                return;
            }
            
            try {
                await axios.delete(`${API_BASE_URL}/subjects/${subject.id}`);
                this.message = 'Ders başarıyla silindi';
                this.loadSubjects();
            } catch (error) {
                this.error = error.response?.data?.message || 'Ders silinemedi';
            }
        },
        
        // ===== Schedules Management =====
        async loadSchedules() {
            this.schedulesLoading = true;
            try {
                const response = await axios.get(`${API_BASE_URL}/schedules`);
                
                if (response.data.data) {
                    this.schedules = response.data.data;
                } else {
                    this.schedules = response.data;
                }
                
            } catch (error) {
                this.error = 'Ders programları yüklenemedi';
                console.error('Schedules load error:', error);
            } finally {
                this.schedulesLoading = false;
            }
        },
        
        editSchedule(schedule) {
            this.editScheduleData = { ...schedule };
            this.editScheduleModal = true;
        },
        
        async updateSchedule() {
            try {
                await axios.put(`${API_BASE_URL}/schedules/${this.editScheduleData.id}`, this.editScheduleData);
                this.message = 'Ders programı başarıyla güncellendi';
                this.editScheduleModal = false;
                this.loadSchedules();
            } catch (error) {
                this.error = error.response?.data?.message || 'Ders programı güncellenemedi';
            }
        },
        
        async deleteSchedule(schedule) {
            if (!confirm('Bu ders programını silmek istediğinizden emin misiniz?')) {
                return;
            }
            
            try {
                await axios.delete(`${API_BASE_URL}/schedules/${schedule.id}`);
                this.message = 'Ders programı başarıyla silindi';
                this.loadSchedules();
            } catch (error) {
                this.error = error.response?.data?.message || 'Ders programı silinemedi';
            }
        },
        
        // ===== Tab Management =====
        changeTab(tab) {
            // Super admin sınıf/ders/program/okul ayarları yönetimine erişemez
            if (this.user && this.user.role?.name === 'super_admin') {
                if (['classes', 'subjects', 'schedules', 'school-settings'].includes(tab)) {
                    this.error = 'Bu bölüme erişim yetkiniz yok. Lütfen bir okul yöneticisi olarak giriş yapın.';
                    return;
                }
            }
            
            this.activeTab = tab;
            this.searchQuery = '';
            
            // Okul ayarları sekmesine geçildiğinde ayarları ve sınıfları yükle
            if (tab === 'school-settings') {
                this.loadSchoolSettings();
                this.loadClasses();
            }
            this.filterStatus = 'all';
            this.currentPage = 1;
            
            // Load data based on active tab
            switch(tab) {
                case 'schools':
                    this.loadSchools();
                    break;
                case 'users':
                    this.loadUsers();
                    break;
                case 'classes':
                    this.loadClasses();
                    break;
                case 'subjects':
                    this.loadSubjects();
                    break;
                case 'schedules':
                    this.loadSchedules();
                    break;
                case 'dashboard':
                    this.loadDashboard();
                    break;
            }
        },
        
        // ===== Classes Management =====
        async loadClasses() {
            this.classesLoading = true;
            try {
                const response = await axios.get(`${API_BASE_URL}/classes`);
                // API direkt array dönüyor (pagination yok)
                this.classes = response.data;
            } catch (error) {
                this.error = 'Sınıflar yüklenemedi';
                console.error('Classes load error:', error);
            } finally {
                this.classesLoading = false;
            }
        },
        
        async openAddClassModal() {
            this.modalError = '';
            await this.loadTeachers();
            this.addClassModal = true;
        },
        
        async loadTeachers() {
            try {
                const response = await axios.get(`${API_BASE_URL}/teachers`);
                this.teachers = response.data;
            } catch (error) {
                console.error('Teachers load error:', error);
            }
        },
        
        async addClass() {
            this.modalError = '';
            try {
                const response = await axios.post(`${API_BASE_URL}/classes`, this.newClass);
                this.message = 'Sınıf başarıyla eklendi';
                this.addClassModal = false;
                
                // Yeni eklenen sınıfı direkt listeye ekle (daha hızlı)
                if (response.data.class) {
                    // Eğer classes bir array ise push et, pagination varsa başa ekle
                    if (Array.isArray(this.classes)) {
                        this.classes.unshift(response.data.class);
                    } else {
                        // Pagination varsa yenile
                        this.loadClasses();
                    }
                }
                
                this.newClass = {
                    name: '',
                    grade: '',
                    branch: '',
                    class_teacher_id: '',
                    description: ''
                };
            } catch (error) {
                this.modalError = error.response?.data?.message || 'Sınıf eklenemedi';
            }
        },
        
        editClass(classItem) {
            this.modalError = '';
            this.editClassData = { ...classItem };
            this.loadTeachers();
            this.editClassModal = true;
        },
        
        async updateClass() {
            this.modalError = '';
            try {
                const response = await axios.put(`${API_BASE_URL}/classes/${this.editClassData.id}`, this.editClassData);
                this.message = 'Sınıf başarıyla güncellendi';
                this.editClassModal = false;
                
                // Güncellenen sınıfı listede bul ve değiştir (daha hızlı)
                if (response.data.class) {
                    const index = this.classes.findIndex(c => c.id === response.data.class.id);
                    if (index !== -1) {
                        this.classes[index] = response.data.class;
                    } else {
                        this.loadClasses();
                    }
                }
            } catch (error) {
                this.modalError = error.response?.data?.message || 'Sınıf güncellenemedi';
            }
        },
        
        async deleteClass(classItem) {
            if (!confirm(`${classItem.name} sınıfını silmek istediğinizden emin misiniz?`)) {
                return;
            }
            
            try {
                await axios.delete(`${API_BASE_URL}/classes/${classItem.id}`);
                this.message = 'Sınıf başarıyla silindi';
                this.loadClasses();
            } catch (error) {
                this.error = error.response?.data?.message || 'Sınıf silinemedi';
            }
        },
        
        // ===== User Helper Methods =====
        generateShortName() {
            const name = this.newUser.name.trim();
            if (!name) {
                this.newUser.short_name = '';
                return;
            }
            
            const parts = name.split(' ');
            if (parts.length < 2) {
                // Sadece tek kelime varsa, ilk 6 harfi al
                this.newUser.short_name = parts[0].substring(0, 6).toUpperCase();
                return;
            }
            
            // İlk adın ilk 4 harfi + Soyadın ilk 2 harfi - sadece boşsa otomatik doldur
            if (!this.newUser.short_name || this.newUser.short_name.trim() === '') {
                const firstName = parts[0].substring(0, 4).toUpperCase();
                const lastName = parts[parts.length - 1].substring(0, 2).toUpperCase();
                this.newUser.short_name = (firstName + lastName).substring(0, 6);
            }
        },
        
        isTeacherRole(roleId) {
            if (!roleId || !this.roles.length) return false;
            const role = this.roles.find(r => r.id === roleId);
            return role && role.name === 'teacher';
        },
        
        async handleExcelFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.isUploadingExcel = true;
            this.error = '';
            
            try {
                const formData = new FormData();
                formData.append('file', file);
                
                const response = await axios.post(`${API_BASE_URL}/users/import-teachers`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                if (response.data.error_count > 0) {
                    // Kısmi başarı
                    this.message = response.data.message;
                    
                    // Hata detaylarını göster
                    let errorDetails = `\n\nHatalar:\n`;
                    response.data.errors.forEach(err => {
                        errorDetails += `Satır ${err.row}: ${err.errors.join(', ')}\n`;
                    });
                    alert(response.data.message + errorDetails);
                } else {
                    // Tam başarı
                    this.message = `✅ ${response.data.success_count} öğretmen başarıyla eklendi!`;
                }
                
                // Kullanıcı listesini yenile
                this.loadUsers();
                
                // File input'u temizle
                event.target.value = '';
                
            } catch (error) {
                console.error('Excel import error:', error);
                this.error = error.response?.data?.message || 'Excel yüklenirken hata oluştu';
            } finally {
                this.isUploadingExcel = false;
            }
        },
        
        // ===== Utility Methods =====
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('tr-TR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        
        formatDateTime(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString('tr-TR', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        closeModal(modalName) {
            this[modalName] = false;
        },
        
        // ===== School Settings =====
        async loadSchoolSettings() {
            if (this.user?.role?.name === 'super_admin') return;
            
            this.schoolSettingsLoading = true;
            try {
                const token = localStorage.getItem('auth_token');
                console.log('Loading settings with token:', token ? 'Token exists' : 'No token');
                
                const response = await axios.get(`${API_BASE_URL}/school/settings`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                
                this.schoolSettings = {
                    class_days: response.data.class_days || ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    lesson_duration: response.data.lesson_duration || 40,
                    break_durations: response.data.break_durations || {
                        small_break: 10,
                        lunch_break: 20
                    },
                    school_hours: response.data.school_hours || {
                        start_time: '08:00',
                        end_time: '16:00'
                    },
                    weekly_lesson_count: response.data.weekly_lesson_count || 30,
                    schedule_settings: response.data.schedule_settings || {
                        allow_teacher_conflicts: false,
                        allow_classroom_conflicts: false,
                        max_lessons_per_day: 8,
                        min_lessons_per_day: 4
                    },
                    daily_lesson_counts: response.data.daily_lesson_counts || {
                        monday: 8,
                        tuesday: 8,
                        wednesday: 8,
                        thursday: 8,
                        friday: 8
                    },
                    class_daily_lesson_counts: response.data.class_daily_lesson_counts || {},
                    teacher_daily_lesson_counts: response.data.teacher_daily_lesson_counts || {}
                };
                
                console.log('Loaded school settings:', this.schoolSettings);
            } catch (error) {
                console.error('Okul ayarları yüklenemedi:', error);
                this.error = 'Okul ayarları yüklenemedi';
            } finally {
                this.schoolSettingsLoading = false;
            }
        },
        
        async saveSchoolSettings() {
            this.isSavingSettings = true;
            try {
                const token = localStorage.getItem('auth_token');
                console.log('Saving settings with token:', token ? 'Token exists' : 'No token');
                
                const response = await axios.put(`${API_BASE_URL}/school/settings`, this.schoolSettings, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                
                this.message = 'Okul ayarları başarıyla kaydedildi!';
                this.error = '';
                
                // Güncellenmiş ayarları yükle
                this.schoolSettings = response.data.settings;
                
            } catch (error) {
                console.error('Okul ayarları kaydedilemedi:', error);
                this.error = error.response?.data?.message || 'Okul ayarları kaydedilemedi';
            } finally {
                this.isSavingSettings = false;
            }
        },
        
        addClassDailyLessonCounts() {
            if (!this.selectedClassForDailyCount) return;
            
            console.log('Adding class:', this.selectedClassForDailyCount);
            console.log('Current class_daily_lesson_counts:', this.schoolSettings.class_daily_lesson_counts);
            
            // Eğer sınıf zaten varsa, ekleme
            if (this.schoolSettings.class_daily_lesson_counts[this.selectedClassForDailyCount]) {
                this.error = 'Bu sınıf zaten eklenmiş!';
                return;
            }
            
            // Yeni sınıf için günlük ders sayılarını oluştur
            const dailyCounts = {};
            this.weekDays.forEach(day => {
                dailyCounts[day.value] = this.schoolSettings.daily_lesson_counts[day.value] || 8;
            });
            
            console.log('New dailyCounts:', dailyCounts);
            
            // Vue 3'te $set gerekmez, direkt atama yapılabilir
            this.schoolSettings.class_daily_lesson_counts[this.selectedClassForDailyCount] = dailyCounts;
            this.selectedClassForDailyCount = '';
            
            console.log('After adding, class_daily_lesson_counts:', this.schoolSettings.class_daily_lesson_counts);
        },
        
        removeClassDailyLessonCounts(className) {
            // Vue 3'te $delete gerekmez, delete operatörü kullanılır
            delete this.schoolSettings.class_daily_lesson_counts[className];
        },
        
        togglePeriod(className, day, period) {
            // Ders olmayan günlerde işlem yapma
            if (!this.schoolSettings.class_days.includes(day)) return;
            
            const currentCount = this.schoolSettings.class_daily_lesson_counts[className][day] || 0;
            
            // Tıklanan periyot aktifse, onu ve sonrasını kapat
            if (currentCount >= period) {
                this.schoolSettings.class_daily_lesson_counts[className][day] = period - 1;
            } else {
                // Tıklanan periyot pasifse, onu ve öncesini aç
                this.schoolSettings.class_daily_lesson_counts[className][day] = period;
            }
        },
        
        // ===== Class Schedule Modal =====
        openClassScheduleModal(classItem) {
            this.selectedClassForSchedule = classItem;
            
            // Mevcut veriyi yükle veya varsayılan değerleri kullan
            const existingData = this.schoolSettings.class_daily_lesson_counts[classItem.name];
            
            if (existingData) {
                this.classScheduleData = { ...existingData };
            } else {
                // Varsayılan değerler
                this.classScheduleData = {
                    monday: 8,
                    tuesday: 8,
                    wednesday: 8,
                    thursday: 8,
                    friday: 8
                };
            }
            
            this.classScheduleModal = true;
        },
        
        toggleClassSchedulePeriod(day, period) {
            // Ders olmayan günlerde işlem yapma
            if (!this.schoolSettings.class_days.includes(day)) return;
            
            const currentCount = this.classScheduleData[day] || 0;
            
            // Tıklanan periyot aktifse, onu ve sonrasını kapat
            if (currentCount >= period) {
                this.classScheduleData[day] = period - 1;
            } else {
                // Tıklanan periyot pasifse, onu ve öncesini aç
                this.classScheduleData[day] = period;
            }
        },
        
        async saveClassSchedule() {
            try {
                // Veriyi schoolSettings'e kaydet
                this.schoolSettings.class_daily_lesson_counts[this.selectedClassForSchedule.name] = { ...this.classScheduleData };
                
                // API'ye kaydet
                const token = localStorage.getItem('auth_token');
                await axios.put(`${API_BASE_URL}/school/settings`, this.schoolSettings, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                
                this.message = `${this.selectedClassForSchedule.name} sınıfının ders saatleri kaydedildi!`;
                this.classScheduleModal = false;
                
            } catch (error) {
                console.error('Sınıf saatleri kaydedilemedi:', error);
                this.error = error.response?.data?.message || 'Sınıf saatleri kaydedilemedi';
            }
        },
        
        // Öğretmen Ders Saatleri Modal Fonksiyonları
        openTeacherScheduleModal(teacher) {
            this.selectedTeacherForSchedule = teacher;
            
            // Mevcut veriyi yükle veya varsayılan değerleri kullan
            const existingData = this.schoolSettings.teacher_daily_lesson_counts?.[teacher.id];
            
            if (existingData) {
                this.teacherScheduleData = { ...existingData };
            } else {
                // Varsayılan değerler
                this.teacherScheduleData = {
                    monday: 0,
                    tuesday: 0,
                    wednesday: 0,
                    thursday: 0,
                    friday: 0
                };
            }
            
            this.teacherScheduleModal = true;
        },
        
        toggleTeacherSchedulePeriod(day, period) {
            if (!this.schoolSettings.class_days.includes(day)) return;
            
            const currentCount = this.teacherScheduleData[day] || 0;
            
            if (currentCount >= period) {
                this.teacherScheduleData[day] = period - 1;
            } else {
                this.teacherScheduleData[day] = period;
            }
        },
        
        async saveTeacherSchedule() {
            try {
                // teacher_daily_lesson_counts yoksa oluştur
                if (!this.schoolSettings.teacher_daily_lesson_counts) {
                    this.schoolSettings.teacher_daily_lesson_counts = {};
                }
                
                // Veriyi schoolSettings'e kaydet
                this.schoolSettings.teacher_daily_lesson_counts[this.selectedTeacherForSchedule.id] = { ...this.teacherScheduleData };
                
                // API'ye kaydet
                const token = localStorage.getItem('auth_token');
                await axios.put(`${API_BASE_URL}/school/settings`, this.schoolSettings, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                
                this.message = `${this.selectedTeacherForSchedule.name} öğretmeninin ders saatleri kaydedildi!`;
                this.teacherScheduleModal = false;
                
            } catch (error) {
                console.error('Öğretmen saatleri kaydedilemedi:', error);
                this.error = error.response?.data?.message || 'Öğretmen saatleri kaydedilemedi';
            }
        },
        
        // Periyot Saatlerini Hesapla
        getPeriodTime(period) {
            const startTime = this.schoolSettings.school_hours?.start_time || '08:00';
            const lessonDuration = this.schoolSettings.lesson_duration || 40;
            const smallBreak = this.schoolSettings.break_durations?.small_break || 10;
            const lunchBreak = this.schoolSettings.break_durations?.lunch_break || 20;
            const lunchBreakAfterPeriod = 5; // Öğle arası 5. saattan sonra
            
            // Başlangıç saatini parse et
            const [startHour, startMinute] = startTime.split(':').map(Number);
            
            // Toplam geçen dakikayı hesapla
            let totalMinutes = (startHour * 60) + startMinute;
            
            // Her periyot için hesapla
            for (let i = 1; i < period; i++) {
                totalMinutes += lessonDuration; // Ders süresi
                
                // Öğle arası kontrolü
                if (i === lunchBreakAfterPeriod) {
                    totalMinutes += lunchBreak;
                } else {
                    totalMinutes += smallBreak; // Küçük tenefüs
                }
            }
            
            // Başlangıç saati
            const startTotalMinutes = totalMinutes;
            const startH = Math.floor(startTotalMinutes / 60);
            const startM = startTotalMinutes % 60;
            const startTimeStr = `${String(startH).padStart(2, '0')}:${String(startM).padStart(2, '0')}`;
            
            // Bitiş saati
            const endTotalMinutes = totalMinutes + lessonDuration;
            const endH = Math.floor(endTotalMinutes / 60);
            const endM = endTotalMinutes % 60;
            const endTimeStr = `${String(endH).padStart(2, '0')}:${String(endM).padStart(2, '0')}`;
            
            return `${startTimeStr} - ${endTimeStr}`;
        }
    },
    
    watch: {
        message(newVal) {
            if (newVal) {
                setTimeout(() => this.message = '', 4000);
            }
        },
        
        error(newVal) {
            if (newVal) {
                setTimeout(() => this.error = '', 5000);
            }
        },
        
        activeTab(newTab) {
            // Auto-load data when tab changes
            this.changeTab(newTab);
        }
    }
}).mount('#app');

