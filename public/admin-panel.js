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
            this.loadRoles();
            this.addUserModal = true;
        },
        
        async addUser() {
            try {
                await axios.post(`${API_BASE_URL}/users`, this.newUser);
                this.message = 'Kullanıcı başarıyla eklendi';
                this.addUserModal = false;
                this.newUser = {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role_id: '',
                    phone: '',
                    address: ''
                };
                this.loadUsers();
            } catch (error) {
                this.error = error.response?.data?.message || 'Kullanıcı eklenemedi';
            }
        },
        
        editUser(user) {
            this.editUserData = { ...user };
            this.editUserModal = true;
        },
        
        async updateUser() {
            try {
                await axios.put(`${API_BASE_URL}/users/${this.editUserData.id}`, this.editUserData);
                this.message = 'Kullanıcı başarıyla güncellendi';
                this.editUserModal = false;
                this.loadUsers();
            } catch (error) {
                this.error = error.response?.data?.message || 'Kullanıcı güncellenemedi';
            }
        },
        
        async deleteUser(user) {
            if (!confirm(`${user.name} kullanıcısını silmek istediğinizden emin misiniz?`)) {
                return;
            }
            
            try {
                await axios.delete(`${API_BASE_URL}/users/${user.id}`);
                this.message = 'Kullanıcı başarıyla silindi';
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
            this.activeTab = tab;
            this.searchQuery = '';
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
                this.classes = response.data.data || response.data;
            } catch (error) {
                this.error = 'Sınıflar yüklenemedi';
                console.error('Classes load error:', error);
            } finally {
                this.classesLoading = false;
            }
        },
        
        async openAddClassModal() {
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
            try {
                await axios.post(`${API_BASE_URL}/classes`, this.newClass);
                this.message = 'Sınıf başarıyla eklendi';
                this.addClassModal = false;
                this.newClass = {
                    name: '',
                    grade: '',
                    branch: '',
                    class_teacher_id: '',
                    description: ''
                };
                this.loadClasses();
            } catch (error) {
                this.error = error.response?.data?.message || 'Sınıf eklenemedi';
            }
        },
        
        editClass(classItem) {
            this.editClassData = { ...classItem };
            this.loadTeachers();
            this.editClassModal = true;
        },
        
        async updateClass() {
            try {
                await axios.put(`${API_BASE_URL}/classes/${this.editClassData.id}`, this.editClassData);
                this.message = 'Sınıf başarıyla güncellendi';
                this.editClassModal = false;
                this.loadClasses();
            } catch (error) {
                this.error = error.response?.data?.message || 'Sınıf güncellenemedi';
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
            
            // İlk adın ilk 4 harfi + Soyadın ilk 2 harfi
            const firstName = parts[0].substring(0, 4).toUpperCase();
            const lastName = parts[parts.length - 1].substring(0, 2).toUpperCase();
            this.newUser.short_name = (firstName + lastName).substring(0, 6);
        },
        
        isTeacherRole(roleId) {
            if (!roleId || !this.roles.length) return false;
            const role = this.roles.find(r => r.id === roleId);
            return role && role.name === 'teacher';
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

