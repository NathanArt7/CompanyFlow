
import type { DefineComponent, SlotsType } from 'vue'
type IslandComponent<T> = DefineComponent<{}, {refresh: () => Promise<void>}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, SlotsType<{ fallback: { error: unknown } }>> & T

type HydrationStrategies = {
  hydrateOnVisible?: IntersectionObserverInit | true
  hydrateOnIdle?: number | true
  hydrateOnInteraction?: keyof HTMLElementEventMap | Array<keyof HTMLElementEventMap> | true
  hydrateOnMediaQuery?: string
  hydrateAfter?: number
  hydrateWhen?: boolean
  hydrateNever?: true
}
type LazyComponent<T> = DefineComponent<HydrationStrategies, {}, {}, {}, {}, {}, {}, { hydrated: () => void }> & T

interface _GlobalComponents {
  AppHeader: typeof import("../../app/components/layout/AppHeader.vue")['default']
  AppSidebar: typeof import("../../app/components/layout/AppSidebar.vue")['default']
  Logo: typeof import("../../app/components/ui/Logo.vue")['default']
  CreateCompanyForm: typeof import("../../app/features/auth/components/CreateCompanyForm.vue")['default']
  CreateSuperAdminForm: typeof import("../../app/features/auth/components/CreateSuperAdminForm.vue")['default']
  LoginForm: typeof import("../../app/features/auth/components/LoginForm.vue")['default']
  ReservationSettingsForm: typeof import("../../app/features/auth/components/ReservationSettingsForm.vue")['default']
  SetPasswordForm: typeof import("../../app/features/auth/components/SetPasswordForm.vue")['default']
  Type: typeof import("../../app/features/auth/type")['default']
  CalendarWidget: typeof import("../../app/features/dashboard/components/CalendarWidget.vue")['default']
  DashboardHeader: typeof import("../../app/features/dashboard/components/DashboardHeader.vue")['default']
  MiniCalendar: typeof import("../../app/features/dashboard/components/MiniCalendar.vue")['default']
  RecentActivity: typeof import("../../app/features/dashboard/components/RecentActivity.vue")['default']
  ReservationStatusBadge: typeof import("../../app/features/dashboard/components/ReservationStatusBadge.vue")['default']
  RoomOccupancy: typeof import("../../app/features/dashboard/components/RoomOccupancy.vue")['default']
  StatCard: typeof import("../../app/features/dashboard/components/StatCard.vue")['default']
  StatsGrid: typeof import("../../app/features/dashboard/components/StatsGrid.vue")['default']
  TodoWidget: typeof import("../../app/features/dashboard/components/TodoWidget.vue")['default']
  UpcomingReservations: typeof import("../../app/features/dashboard/components/UpcomingReservations.vue")['default']
  EquipmentStateBadge: typeof import("../../app/features/equipments/components/EquipmentStateBadge.vue")['default']
  EquipmentsFilters: typeof import("../../app/features/equipments/components/EquipmentsFilters.vue")['default']
  EquipmentsHeader: typeof import("../../app/features/equipments/components/EquipmentsHeader.vue")['default']
  EquipmentsStats: typeof import("../../app/features/equipments/components/EquipmentsStats.vue")['default']
  EquipmentsTable: typeof import("../../app/features/equipments/components/EquipmentsTable.vue")['default']
  UsageTypeBadge: typeof import("../../app/features/equipments/components/UsageTypeBadge.vue")['default']
  MarketingCallToAction: typeof import("../../app/features/marketing/components/MarketingCallToAction.vue")['default']
  MarketingDashboardPreview: typeof import("../../app/features/marketing/components/MarketingDashboardPreview.vue")['default']
  MarketingFeatureCard: typeof import("../../app/features/marketing/components/MarketingFeatureCard.vue")['default']
  MarketingFeaturesGrid: typeof import("../../app/features/marketing/components/MarketingFeaturesGrid.vue")['default']
  MarketingFooter: typeof import("../../app/features/marketing/components/MarketingFooter.vue")['default']
  MarketingHeroSection: typeof import("../../app/features/marketing/components/MarketingHeroSection.vue")['default']
  MarketingNavbar: typeof import("../../app/features/marketing/components/MarketingNavbar.vue")['default']
  MarketingOnboardingStep: typeof import("../../app/features/marketing/components/MarketingOnboardingStep.vue")['default']
  MarketingOnboardingSteps: typeof import("../../app/features/marketing/components/MarketingOnboardingSteps.vue")['default']
  DailySummary: typeof import("../../app/features/reservations/components/DailySummary.vue")['default']
  ReservationsFilters: typeof import("../../app/features/reservations/components/ReservationsFilters.vue")['default']
  ReservationsHeader: typeof import("../../app/features/reservations/components/ReservationsHeader.vue")['default']
  ReservationsTable: typeof import("../../app/features/reservations/components/ReservationsTable.vue")['default']
  WeekStrip: typeof import("../../app/features/reservations/components/WeekStrip.vue")['default']
  RoomStatusBadge: typeof import("../../app/features/rooms/components/RoomStatusBadge.vue")['default']
  RoomTypeBadge: typeof import("../../app/features/rooms/components/RoomTypeBadge.vue")['default']
  RoomsFilters: typeof import("../../app/features/rooms/components/RoomsFilters.vue")['default']
  RoomsHeader: typeof import("../../app/features/rooms/components/RoomsHeader.vue")['default']
  RoomsStats: typeof import("../../app/features/rooms/components/RoomsStats.vue")['default']
  RoomsTable: typeof import("../../app/features/rooms/components/RoomsTable.vue")['default']
  Types: typeof import("../../app/features/rooms/types")['default']
  RoleBadge: typeof import("../../app/features/users/components/RoleBadge.vue")['default']
  UserStatusBadge: typeof import("../../app/features/users/components/UserStatusBadge.vue")['default']
  UsersFilters: typeof import("../../app/features/users/components/UsersFilters.vue")['default']
  UsersHeader: typeof import("../../app/features/users/components/UsersHeader.vue")['default']
  UsersStats: typeof import("../../app/features/users/components/UsersStats.vue")['default']
  UsersTable: typeof import("../../app/features/users/components/UsersTable.vue")['default']
  NuxtWelcome: typeof import("../../node_modules/nuxt/dist/app/components/welcome.vue")['default']
  NuxtLayout: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-layout")['default']
  NuxtErrorBoundary: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
  ClientOnly: typeof import("../../node_modules/nuxt/dist/app/components/client-only")['default']
  DevOnly: typeof import("../../node_modules/nuxt/dist/app/components/dev-only")['default']
  ServerPlaceholder: typeof import("../../node_modules/nuxt/dist/app/components/server-placeholder")['default']
  NuxtLink: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-link")['default']
  NuxtLoadingIndicator: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
  NuxtTime: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
  NuxtRouteAnnouncer: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
  NuxtAnnouncer: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']
  NuxtImg: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
  NuxtPicture: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
  NuxtPage: typeof import("../../node_modules/nuxt/dist/pages/runtime/page")['default']
  NoScript: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['NoScript']
  Link: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Link']
  Base: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Base']
  Title: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Title']
  Meta: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Meta']
  Style: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Style']
  Head: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Head']
  Html: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Html']
  Body: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Body']
  NuxtIsland: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-island")['default']
  LazyAppHeader: LazyComponent<typeof import("../../app/components/layout/AppHeader.vue")['default']>
  LazyAppSidebar: LazyComponent<typeof import("../../app/components/layout/AppSidebar.vue")['default']>
  LazyLogo: LazyComponent<typeof import("../../app/components/ui/Logo.vue")['default']>
  LazyCreateCompanyForm: LazyComponent<typeof import("../../app/features/auth/components/CreateCompanyForm.vue")['default']>
  LazyCreateSuperAdminForm: LazyComponent<typeof import("../../app/features/auth/components/CreateSuperAdminForm.vue")['default']>
  LazyLoginForm: LazyComponent<typeof import("../../app/features/auth/components/LoginForm.vue")['default']>
  LazyReservationSettingsForm: LazyComponent<typeof import("../../app/features/auth/components/ReservationSettingsForm.vue")['default']>
  LazySetPasswordForm: LazyComponent<typeof import("../../app/features/auth/components/SetPasswordForm.vue")['default']>
  LazyType: LazyComponent<typeof import("../../app/features/auth/type")['default']>
  LazyCalendarWidget: LazyComponent<typeof import("../../app/features/dashboard/components/CalendarWidget.vue")['default']>
  LazyDashboardHeader: LazyComponent<typeof import("../../app/features/dashboard/components/DashboardHeader.vue")['default']>
  LazyMiniCalendar: LazyComponent<typeof import("../../app/features/dashboard/components/MiniCalendar.vue")['default']>
  LazyRecentActivity: LazyComponent<typeof import("../../app/features/dashboard/components/RecentActivity.vue")['default']>
  LazyReservationStatusBadge: LazyComponent<typeof import("../../app/features/dashboard/components/ReservationStatusBadge.vue")['default']>
  LazyRoomOccupancy: LazyComponent<typeof import("../../app/features/dashboard/components/RoomOccupancy.vue")['default']>
  LazyStatCard: LazyComponent<typeof import("../../app/features/dashboard/components/StatCard.vue")['default']>
  LazyStatsGrid: LazyComponent<typeof import("../../app/features/dashboard/components/StatsGrid.vue")['default']>
  LazyTodoWidget: LazyComponent<typeof import("../../app/features/dashboard/components/TodoWidget.vue")['default']>
  LazyUpcomingReservations: LazyComponent<typeof import("../../app/features/dashboard/components/UpcomingReservations.vue")['default']>
  LazyEquipmentStateBadge: LazyComponent<typeof import("../../app/features/equipments/components/EquipmentStateBadge.vue")['default']>
  LazyEquipmentsFilters: LazyComponent<typeof import("../../app/features/equipments/components/EquipmentsFilters.vue")['default']>
  LazyEquipmentsHeader: LazyComponent<typeof import("../../app/features/equipments/components/EquipmentsHeader.vue")['default']>
  LazyEquipmentsStats: LazyComponent<typeof import("../../app/features/equipments/components/EquipmentsStats.vue")['default']>
  LazyEquipmentsTable: LazyComponent<typeof import("../../app/features/equipments/components/EquipmentsTable.vue")['default']>
  LazyUsageTypeBadge: LazyComponent<typeof import("../../app/features/equipments/components/UsageTypeBadge.vue")['default']>
  LazyMarketingCallToAction: LazyComponent<typeof import("../../app/features/marketing/components/MarketingCallToAction.vue")['default']>
  LazyMarketingDashboardPreview: LazyComponent<typeof import("../../app/features/marketing/components/MarketingDashboardPreview.vue")['default']>
  LazyMarketingFeatureCard: LazyComponent<typeof import("../../app/features/marketing/components/MarketingFeatureCard.vue")['default']>
  LazyMarketingFeaturesGrid: LazyComponent<typeof import("../../app/features/marketing/components/MarketingFeaturesGrid.vue")['default']>
  LazyMarketingFooter: LazyComponent<typeof import("../../app/features/marketing/components/MarketingFooter.vue")['default']>
  LazyMarketingHeroSection: LazyComponent<typeof import("../../app/features/marketing/components/MarketingHeroSection.vue")['default']>
  LazyMarketingNavbar: LazyComponent<typeof import("../../app/features/marketing/components/MarketingNavbar.vue")['default']>
  LazyMarketingOnboardingStep: LazyComponent<typeof import("../../app/features/marketing/components/MarketingOnboardingStep.vue")['default']>
  LazyMarketingOnboardingSteps: LazyComponent<typeof import("../../app/features/marketing/components/MarketingOnboardingSteps.vue")['default']>
  LazyDailySummary: LazyComponent<typeof import("../../app/features/reservations/components/DailySummary.vue")['default']>
  LazyReservationsFilters: LazyComponent<typeof import("../../app/features/reservations/components/ReservationsFilters.vue")['default']>
  LazyReservationsHeader: LazyComponent<typeof import("../../app/features/reservations/components/ReservationsHeader.vue")['default']>
  LazyReservationsTable: LazyComponent<typeof import("../../app/features/reservations/components/ReservationsTable.vue")['default']>
  LazyWeekStrip: LazyComponent<typeof import("../../app/features/reservations/components/WeekStrip.vue")['default']>
  LazyRoomStatusBadge: LazyComponent<typeof import("../../app/features/rooms/components/RoomStatusBadge.vue")['default']>
  LazyRoomTypeBadge: LazyComponent<typeof import("../../app/features/rooms/components/RoomTypeBadge.vue")['default']>
  LazyRoomsFilters: LazyComponent<typeof import("../../app/features/rooms/components/RoomsFilters.vue")['default']>
  LazyRoomsHeader: LazyComponent<typeof import("../../app/features/rooms/components/RoomsHeader.vue")['default']>
  LazyRoomsStats: LazyComponent<typeof import("../../app/features/rooms/components/RoomsStats.vue")['default']>
  LazyRoomsTable: LazyComponent<typeof import("../../app/features/rooms/components/RoomsTable.vue")['default']>
  LazyTypes: LazyComponent<typeof import("../../app/features/rooms/types")['default']>
  LazyRoleBadge: LazyComponent<typeof import("../../app/features/users/components/RoleBadge.vue")['default']>
  LazyUserStatusBadge: LazyComponent<typeof import("../../app/features/users/components/UserStatusBadge.vue")['default']>
  LazyUsersFilters: LazyComponent<typeof import("../../app/features/users/components/UsersFilters.vue")['default']>
  LazyUsersHeader: LazyComponent<typeof import("../../app/features/users/components/UsersHeader.vue")['default']>
  LazyUsersStats: LazyComponent<typeof import("../../app/features/users/components/UsersStats.vue")['default']>
  LazyUsersTable: LazyComponent<typeof import("../../app/features/users/components/UsersTable.vue")['default']>
  LazyNuxtWelcome: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/welcome.vue")['default']>
  LazyNuxtLayout: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
  LazyNuxtErrorBoundary: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
  LazyClientOnly: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/client-only")['default']>
  LazyDevOnly: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/dev-only")['default']>
  LazyServerPlaceholder: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
  LazyNuxtLink: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-link")['default']>
  LazyNuxtLoadingIndicator: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
  LazyNuxtTime: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
  LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
  LazyNuxtAnnouncer: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']>
  LazyNuxtImg: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
  LazyNuxtPicture: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
  LazyNuxtPage: LazyComponent<typeof import("../../node_modules/nuxt/dist/pages/runtime/page")['default']>
  LazyNoScript: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['NoScript']>
  LazyLink: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Link']>
  LazyBase: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Base']>
  LazyTitle: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Title']>
  LazyMeta: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Meta']>
  LazyStyle: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Style']>
  LazyHead: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Head']>
  LazyHtml: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Html']>
  LazyBody: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Body']>
  LazyNuxtIsland: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-island")['default']>
}

declare module 'vue' {
  export interface GlobalComponents extends _GlobalComponents { }
}

export {}
