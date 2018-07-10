<template>
  <div>
    <DarkModeSwitcher />
    <MobileMenu />
    <!-- BEGIN: Top Bar -->
    <div
      class="border-b border-theme-24 -mt-10 md:-mt-5 -mx-3 sm:-mx-8 px-3 sm:px-8 pt-3 md:pt-0 mb-10"
    >
      <div class="top-bar-boxed flex items-center">
        <!-- BEGIN: Logo -->
        <router-link
          :to="{ name: 'dashboard' }"
          tag="a"
          class="-intro-x hidden md:flex"
        >
          <img
            class="logo"
            alt="Midone Tailwind HTML Admin Template"
            src="@/assets/images/logo.png"
          />
        </router-link>
        <!-- END: Logo -->
        <!-- BEGIN: Breadcrumb -->
        <div class="-intro-x breadcrumb breadcrumb--light mr-auto">
          <div
            v-for="(crumb, index) in breadcrumbList"
            :key="index"
            class="flex items-center"
          >
            <ChevronRightIcon v-if="index != 0" class="breadcrumb__icon" />
            <a
              :href="crumb.link"
              :class="
                index == breadcrumbList.length - 1 ? 'breadcrumb--active' : ''
              "
              >{{ crumb.name }}</a
            >
          </div>
        </div>
        <!-- END: Breadcrumb -->
        <!-- BEGIN: Search -->
        <div class="intro-x relative mr-3 sm:mr-6">
          <div class="search hidden sm:block">
            <input
              type="text"
              class="search__input input dark:bg-dark-1 placeholder-theme-13"
              placeholder="Search..."
              @focus="showSearchDropdown"
              @blur="hideSearchDropdown"
            />
            <SearchIcon class="search__icon dark:text-gray-300" />
          </div>
          <a class="notification notification--light sm:hidden" href="">
            <SearchIcon class="notification__icon dark:text-gray-300" />
          </a>
          <div class="search-result" :class="{ show: searchDropdown }">
            <div class="search-result__content">
              <div class="search-result__content__title">Pages</div>
              <div class="mb-5">
                <a href class="flex items-center">
                  <div
                    class="w-8 h-8 bg-theme-18 text-theme-9 flex items-center justify-center rounded-full"
                  >
                    <InboxIcon class="w-4 h-4" />
                  </div>
                  <div class="ml-3">Mail Settings</div>
                </a>
                <a href class="flex items-center mt-2">
                  <div
                    class="w-8 h-8 bg-theme-17 text-theme-11 flex items-center justify-center rounded-full"
                  >
                    <UsersIcon class="w-4 h-4" />
                  </div>
                  <div class="ml-3">Users & Permissions</div>
                </a>
                <a href class="flex items-center mt-2">
                  <div
                    class="w-8 h-8 bg-theme-14 text-theme-10 flex items-center justify-center rounded-full"
                  >
                    <CreditCardIcon class="w-4 h-4" />
                  </div>
                  <div class="ml-3">Transactions Report</div>
                </a>
              </div>
              <div class="search-result__content__title">Users</div>
              <div class="mb-5">
                <a
                  v-for="(faker, fakerKey) in $_.take($f(), 4)"
                  :key="fakerKey"
                  href
                  class="flex items-center mt-2"
                >
                  <div class="w-8 h-8 image-fit">
                    <img
                      alt="Midone Tailwind HTML Admin Template"
                      class="rounded-full"
                      :src="require(`@/assets/images/${faker.photos[0]}`)"
                    />
                  </div>
                  <div class="ml-3">{{ faker.users[0].name }}</div>
                  <div
                    class="ml-auto w-48 truncate text-gray-600 text-xs text-right"
                  >
                    {{ faker.users[0].email }}
                  </div>
                </a>
              </div>
              <div class="search-result__content__title">Products</div>
              <a
                v-for="(faker, fakerKey) in $_.take($f(), 4)"
                :key="fakerKey"
                href
                class="flex items-center mt-2"
              >
                <div class="w-8 h-8 image-fit">
                  <img
                    alt="Midone Tailwind HTML Admin Template"
                    class="rounded-full"
                    :src="require(`@/assets/images/${faker.images[0]}`)"
                  />
                </div>
                <div class="ml-3">{{ faker.products[0].name }}</div>
                <div
                  class="ml-auto w-48 truncate text-gray-600 text-xs text-right"
                >
                  {{ faker.products[0].category }}
                </div>
              </a>
            </div>
          </div>
        </div>
        <!-- END: Search -->
        <!-- BEGIN: Notifications -->
        <div class="intro-x dropdown relative mr-4 sm:mr-6">
          <div
            class="dropdown-toggle notification notification--light notification--bullet cursor-pointer"
          >
            <BellIcon class="notification__icon dark:text-gray-300" />
          </div>
          <div class="notification-content pt-2 dropdown-box">
            <div
              class="notification-content__box dropdown-box__content box dark:bg-dark-6"
            >
              <div class="notification-content__title">Notifications</div>
              <div
                v-for="(faker, fakerKey) in $_.take($f(), 5)"
                :key="fakerKey"
                class="cursor-pointer relative flex items-center"
                :class="{ 'mt-5': fakerKey }"
              >
                <div class="w-12 h-12 flex-none image-fit mr-1">
                  <img
                    alt="Midone Tailwind HTML Admin Template"
                    class="rounded-full"
                    :src="require(`@/assets/images/${faker.photos[0]}`)"
                  />
                  <div
                    class="w-3 h-3 bg-theme-9 absolute right-0 bottom-0 rounded-full border-2 border-white"
                  ></div>
                </div>
                <div class="ml-2 overflow-hidden">
                  <div class="flex items-center">
                    <a href="javascript:;" class="font-medium truncate mr-5">
                      {{ faker.users[0].name }}
                    </a>
                    <div
                      class="text-xs text-gray-500 ml-auto whitespace-no-wrap"
                    >
                      {{ faker.times[0] }}
                    </div>
                  </div>
                  <div class="w-full truncate text-gray-600">
                    {{ faker.news[0].short_content }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END: Notifications -->
        <!-- BEGIN: Account Menu -->
        <div class="intro-x dropdown w-8 h-8 relative">
          <div
            class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110"
          >
            <img
              alt="Midone Tailwind HTML Admin Template"
              :src="require(`@/assets/images/${$f()[9].photos[0]}`)"
            />
          </div>
          <div class="dropdown-box w-56">
            <div
              class="dropdown-box__content box bg-theme-38 dark:bg-dark-6 text-white"
            >
              <div class="p-4 border-b border-theme-40 dark:border-dark-3">
                <div class="font-medium">
                  {{ name }}
                </div>
                <div class="text-xs text-theme-41 dark:text-gray-600">
                  {{ uid }}
                </div>
              </div>
              <div class="p-2">
                <a
                  href=""
                  class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md"
                >
                  <UserIcon class="w-4 h-4 mr-2" /> Profile
                </a>
                <a
                  href=""
                  class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md"
                >
                  <EditIcon class="w-4 h-4 mr-2" /> Add Account
                </a>
                <a
                  href=""
                  class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md"
                >
                  <LockIcon class="w-4 h-4 mr-2" /> Reset Password
                </a>
                <a
                  href=""
                  class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md"
                >
                  <HelpCircleIcon class="w-4 h-4 mr-2" />
                  Help
                </a>
              </div>
              <div class="p-2 border-t border-theme-40 dark:border-dark-3">
                <a
                  class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md"
                  @click="logout"
                >
                  <ToggleRightIcon class="w-4 h-4 mr-2" />
                  Logout
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- END: Account Menu -->
      </div>
    </div>
    <!-- END: Top Bar -->
    <!-- BEGIN: Top Menu -->
    <nav class="top-nav">
      <ul>
        <!-- BEGIN: First Child -->
        <template v-for="(menu, menuKey) in formattedMenu">
          <li v-if="menu.hasPermission" :key="menuKey">
            <a
              href="javascript:;"
              class="top-menu"
              :class="{
                'top-menu--active': menu.active
              }"
              @click="linkTo(menu)"
            >
              <div class="top-menu__icon">
                <component :is="menu.icon" />
              </div>
              <div class="top-menu__title">
                {{ menu.title }}
                <ChevronDownIcon
                  v-if="$h.isset(menu.subMenu)"
                  class="top-menu__sub-icon"
                />
              </div>
            </a>
            <!-- BEGIN: Second Child -->
            <ul v-if="$h.isset(menu.subMenu)">
              <li
                v-for="(subMenu, subMenuKey) in menu.subMenu"
                :key="subMenuKey"
              >
                <a
                  v-if="subMenu.hasPermission"
                  href="javascript:;"
                  class="top-menu"
                  @click="linkTo(subMenu)"
                >
                  <div class="top-menu__icon">
                    <component :is="subMenu.icon" />
                  </div>
                  <div class="top-menu__title">
                    {{ subMenu.title }}
                    <ChevronDownIcon
                      v-if="$h.isset(subMenu.subMenu)"
                      class="top-menu__sub-icon"
                    />
                  </div>
                </a>
                <!-- BEGIN: Third Child -->
                <ul v-if="$h.isset(subMenu.subMenu)">
                  <li
                    v-for="(lastSubMenu, lastSubMenuKey) in subMenu.subMenu"
                    :key="lastSubMenuKey"
                  >
                    <a
                      v-if="lastSubMenu.hasPermission"
                      href="javascript:;"
                      class="top-menu"
                      @click="linkTo(lastSubMenu)"
                    >
                      <div class="top-menu__icon">
                        <component :is="'zap-icon'" />
                      </div>
                      <div class="top-menu__title">
                        {{ lastSubMenu.title }}
                      </div>
                    </a>
                  </li>
                </ul>
                <!-- END: Third Child -->
              </li>
            </ul>
            <!-- END: Second Child -->
          </li>
        </template>
        <!-- END: First Child -->
      </ul>
    </nav>
    <!-- END: Top Menu -->
    <!-- BEGIN: Content -->
    <div class="content">
      <router-view />
    </div>
    <!-- END: Content -->
  </div>
</template>

<script>
import TopBar from "@/components/TopBar";
import MobileMenu from "@/components/MobileMenu";
import DarkModeSwitcher from "@/components/DarkModeSwitcher";

export default {
  components: {
    DarkModeSwitcher,
    MobileMenu,
    TopBar
  },
  data() {
    return {
      formattedMenu: [],
      searchDropdown: false,
      breadcrumbList: []
    };
  },
  computed: {
    topMenu() {
      return this.nestedMenu(this.$store.state.topMenu.menu);
    },
    name() {
      return (
        this.$store.state.main.user.first_name +
        " " +
        this.$store.state.main.user.last_name
      );
    },
    uid() {
      return this.$store.state.main.user.uid;
    }
  },
  watch: {
    $route() {
      this.formattedMenu = this.$h.assign(this.topMenu);
      this.breadcrumbList = this.$route.meta.breadcrumb;
    }
  },
  mounted() {
    cash("body")
      .removeClass("login")
      .addClass("app");
    this.formattedMenu = this.$h.assign(this.topMenu);
    this.breadcrumbList = this.$route.meta.breadcrumb;
  },
  updated() {},
  methods: {
    logout: function() {
      this.$store.dispatch("main/logout").then(() => {
        this.$router.push("/login");
      });
    },
    showSearchDropdown() {
      this.searchDropdown = true;
    },
    hideSearchDropdown() {
      this.searchDropdown = false;
    },
    nestedMenu(menu) {
      let $this = this;
      menu.forEach((item, key) => {
        if (typeof item !== "string") {
          menu[key].active =
            (item.pageName == this.$route.name ||
              (this.$h.isset(item.subMenu) &&
                this.findActiveMenu(item.subMenu))) &&
            !item.ignore;
          menu[key].hasPermission =
            item.authorize.length <= 0 ||
            item.authorize.filter(function(p) {
              return $this.$store.state.main.permissions.indexOf(p) !== -1;
            }).length > 0;
        }

        if (this.$h.isset(item.subMenu)) {
          menu[key] = {
            ...item,
            ...this.nestedMenu(item.subMenu)
          };
        }
      });

      return menu;
    },
    findActiveMenu(subMenu) {
      let match = false;
      subMenu.forEach(item => {
        if (item.pageName == this.$route.name && !item.ignore) {
          match = true;
        } else if (!match && this.$h.isset(item.subMenu)) {
          match = this.findActiveMenu(item.subMenu);
        }
      });
      return match;
    },
    linkTo(menu) {
      if (!this.$h.isset(menu.subMenu)) {
        this.$router.push({
          name: menu.pageName
        });
      }
    }
  }
};
</script>
