<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <nav
                class="border-b border-gray-200 bg-white/95 backdrop-blur-md shadow-sm"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-14 sm:h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-8 sm:h-9 lg:h-10 w-auto fill-current text-blue-600 transition-transform duration-200 hover:scale-105"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-1 sm:space-x-1 sm:-my-px sm:ms-6 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors duration-200 text-sm"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('votos')"
                                    :active="route().current('votos')"
                                    class="px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors duration-200 text-sm"
                                >
                                    <i class="fas fa-vote-yea mr-1"></i>
                                    Mis Votos
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    :href="route('admin.votos')"
                                    :active="route().current('admin.votos')"
                                    class="px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors duration-200 text-sm"
                                >
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Admin Votos
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-3 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-2 sm:ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-xl">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-xl border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm hover:shadow-md"
                                            >
                                                <span class="hidden sm:inline">{{ $page.props.auth.user.name }}</span>
                                                <span class="sm:hidden">{{ $page.props.auth.user.name.split(' ')[0] }}</span>

                                                <svg
                                                    class="-me-0.5 ms-1 sm:ms-2 h-4 w-4 transition-transform duration-200"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                            class="hover:bg-gray-50 transition-colors duration-200"
                                        >
                                            <i class="fas fa-user mr-2 text-gray-400"></i>
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="hover:bg-red-50 text-red-600 transition-colors duration-200"
                                        >
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-1 sm:-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <svg
                                    class="h-5 w-5 transition-transform duration-200"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <div
                        v-if="showingNavigationDropdown"
                        class="sm:hidden border-t border-gray-200 bg-white"
                    >
                        <div class="px-3 sm:px-4 py-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                                class="hover:bg-blue-50 transition-colors duration-200 text-sm"
                            >
                                <i class="fas fa-home mr-2 text-gray-400"></i>
                                Dashboard
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div
                            class="border-t border-gray-200 px-3 sm:px-4 py-3"
                        >
                            <div class="px-1">
                                <div
                                    class="text-sm font-medium text-gray-900 truncate"
                                >
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-xs font-medium text-gray-500 truncate">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink 
                                    :href="route('profile.edit')"
                                    class="hover:bg-gray-50 transition-colors duration-200 text-sm"
                                >
                                    <i class="fas fa-user mr-2 text-gray-400"></i>
                                    Profile
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="hover:bg-red-50 text-red-600 transition-colors duration-200 text-sm"
                                >
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Log Out
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </transition>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow-sm border-b border-gray-200"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="animate-fade-in">
                <slot />
            </main>
        </div>
    </div>
</template>
