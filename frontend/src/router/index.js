import Vue from 'vue';
import Router from 'vue-router';
import Rooms from '@/views/Rooms';
import Login from '@/views/Login';
import Register from '@/views/Register';
import AuthSection from '@/views/AuthSection';
import Room from '@/views/Room';
import Profile from '@/views/Profile';


Vue.use(Router);

export default new Router({
    routes: [

        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/registrace',
            name: 'register',
            component: Register
        },

        // zabezpecena sekce, vyzaduje prihlaseni
        {
            path: '/auth',
            name: 'auth',
            component: AuthSection,
            beforeEnter: requireAuth,
            children: [
                {
                    path: '/rooms', ///:id
                    name: 'rooms',
                    component: Rooms
                },
                {
                    path: '/room/:id', //:id/
                    name: 'room',
                    component: Room
                },
                {

                    path: '/profile/', //:id
                    name: 'profile',
                    component: Profile
                }
            ]
        },
    ]
});

function requireAuth(to, from, next) {
    const token = localStorage.getItem('token');

    if (token === null || token === undefined) {
        next({name: 'login', params: {nextUrl: to.fullPath}});
    } else {
        next();
    }
}
