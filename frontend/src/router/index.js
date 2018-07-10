import Vue from "vue";
import VueRouter from "vue-router";

import TopMenu from "../layouts/TopMenu";
import Dashboard from "../views/Dashboard";
import Login from "../views/Login";
import Register from "../views/Register";
import ErrorPage from "../views/ErrorPage";
import Permissions from "../views/Permissions";
import Roles from "../views/Roles";
import Users from "../views/Users";
import Addresses from "../views/Addresses";
import ChangePwd from "../views/ChangePwd";
import KycUpload from "../views/KycUpload";
import Invoices from "../views/Invoices";

import Role from "../utils/roles";

const originalPush = VueRouter.prototype.push;
VueRouter.prototype.push = function push(location) {
  return originalPush.call(this, location).catch(err => err);
};

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    component: TopMenu,
    meta: {
      requiresAuth: true
    },
    children: [
      {
        path: "/",
        name: "dashboard",
        component: Dashboard,
        meta: {
          authorize: [],
          breadcrumb: [{ name: "Afroshipper", link: "" }, { name: "Dashboard" }]
        }
      },
      {
        path: "/permissions",
        name: "permissions",
        component: Permissions,
        meta: {
          authorize: ["permission_list"],
          breadcrumb: [
            { name: "Afroshipper", link: "" },
            { name: "User & Role", link: "users" },
            { name: "Permissions" }
          ]
        }
      },
      {
        path: "/roles",
        name: "roles",
        component: Roles,
        meta: {
          authorize: ["role_list"],
          breadcrumb: [
            { name: "Afroshipper", link: "" },
            { name: "User & Role", link: "users" },
            { name: "Roles" }
          ]
        }
      },
      {
        path: "/users",
        name: "users",
        component: Users,
        meta: {
          authorize: ["user_list"],
          breadcrumb: [
            { name: "Afroshipper", link: "" },
            { name: "User & Role", link: "users" },
            { name: "Users" }
          ]
        }
      },
      {
        path: "/addresses",
        name: "addresses",
        component: Addresses,
        meta: {
          authorize: ["address_list"],
          breadcrumb: [
            { name: "Afroshipper", link: "" },
            { name: "My Account", link: "profile" },
            { name: "My Addresses" }
          ]
        }
      },
      {
        path: "/changepwd",
        name: "changepwd",
        component: ChangePwd,
        meta: {
          authorize: [],
          breadcrumb: [
            { name: "Afroshipper", link: "" },
            { name: "My Account", link: "profile" },
            { name: "Change Password" }
          ]
        }
      },
      {
        path: "/kyc-upload",
        name: "kyc-upload",
        component: KycUpload,
        meta: {
          authorize: ["kyc_upload"],
          breadcrumb: [
            { name: "Afroshipper", link: "" },
            { name: "My Account", link: "profile" },
            { name: "KYC Verification" }
          ]
        }
      },
      {
        path: "/invoices",
        name: "invoices",
        component: Invoices,
        meta: {
          authorize: ["invoice_list"],
          breadcrumb: [{ name: "Afroshipper", link: "" }, { name: "Invoices" }]
        }
      }
    ]
  },
  {
    path: "/login",
    name: "login",
    component: Login
  },
  {
    path: "/register",
    name: "register",
    component: Register
  },
  {
    path: "/error-page",
    name: "error-page",
    component: ErrorPage
  },
  {
    path: "/*",
    component: ErrorPage
  }
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { x: 0, y: 0 };
    }
  }
});

router.beforeEach((to, from, next) => {
  let permissions = JSON.parse(localStorage.getItem("permissions"));
  const { authorize } = to.meta;

  if (authorize) {
    if (!permissions) {
      // not logged in so redirect to login page with the return url
      return next({ path: "/login", query: { returnUrl: to.path } });
    }

    // check if route is restricted by role
    if (authorize.length) {
      let isAuthorized =
        authorize.filter(function(p) {
          return permissions.indexOf(p) !== -1;
        }).length > 0;
      // role not authorised so redirect to home page
      if (!isAuthorized) return next({ path: "/error-page" });
    }
  }

  next();
});

export default router;
