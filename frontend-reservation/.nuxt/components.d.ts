
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


export const AppHeader: typeof import("../app/components/layout/AppHeader.vue")['default']
export const AppSidebar: typeof import("../app/components/layout/AppSidebar.vue")['default']
export const Logo: typeof import("../app/components/ui/Logo.vue")['default']
export const CreateCompanyForm: typeof import("../app/features/auth/components/CreateCompanyForm.vue")['default']
export const CreateSuperAdminForm: typeof import("../app/features/auth/components/CreateSuperAdminForm.vue")['default']
export const LoginForm: typeof import("../app/features/auth/components/LoginForm.vue")['default']
export const ReservationSettingsForm: typeof import("../app/features/auth/components/ReservationSettingsForm.vue")['default']
export const SetPasswordForm: typeof import("../app/features/auth/components/SetPasswordForm.vue")['default']
export const Type: typeof import("../app/features/auth/type")['default']
export const AvailabilityGrid: typeof import("../app/features/availability/components/AvailabilityGrid.vue")['default']
export const AvailabilityHeader: typeof import("../app/features/availability/components/AvailabilityHeader.vue")['default']
export const AvailabilityLegend: typeof import("../app/features/availability/components/AvailabilityLegend.vue")['default']
export const AvailabilityNav: typeof import("../app/features/availability/components/AvailabilityNav.vue")['default']
export const AvailabilityStats: typeof import("../app/features/availability/components/AvailabilityStats.vue")['default']
export const EquipmentAvailabilityGrid: typeof import("../app/features/availability/components/EquipmentAvailabilityGrid.vue")['default']
export const Types: typeof import("../app/features/availability/types")['default']
export const CalendarWidget: typeof import("../app/features/dashboard/components/CalendarWidget.vue")['default']
export const DashboardHeader: typeof import("../app/features/dashboard/components/DashboardHeader.vue")['default']
export const MiniCalendar: typeof import("../app/features/dashboard/components/MiniCalendar.vue")['default']
export const RecentActivity: typeof import("../app/features/dashboard/components/RecentActivity.vue")['default']
export const ReservationStatusBadge: typeof import("../app/features/dashboard/components/ReservationStatusBadge.vue")['default']
export const RoomOccupancy: typeof import("../app/features/dashboard/components/RoomOccupancy.vue")['default']
export const StatCard: typeof import("../app/features/dashboard/components/StatCard.vue")['default']
export const StatsGrid: typeof import("../app/features/dashboard/components/StatsGrid.vue")['default']
export const TodoWidget: typeof import("../app/features/dashboard/components/TodoWidget.vue")['default']
export const UpcomingReservations: typeof import("../app/features/dashboard/components/UpcomingReservations.vue")['default']
export const EquipmentStateBadge: typeof import("../app/features/equipments/components/EquipmentStateBadge.vue")['default']
export const EquipmentsFilters: typeof import("../app/features/equipments/components/EquipmentsFilters.vue")['default']
export const EquipmentsHeader: typeof import("../app/features/equipments/components/EquipmentsHeader.vue")['default']
export const EquipmentsStats: typeof import("../app/features/equipments/components/EquipmentsStats.vue")['default']
export const EquipmentsTable: typeof import("../app/features/equipments/components/EquipmentsTable.vue")['default']
export const UsageTypeBadge: typeof import("../app/features/equipments/components/UsageTypeBadge.vue")['default']
export const MarketingCallToAction: typeof import("../app/features/marketing/components/MarketingCallToAction.vue")['default']
export const MarketingDashboardPreview: typeof import("../app/features/marketing/components/MarketingDashboardPreview.vue")['default']
export const MarketingFeatureCard: typeof import("../app/features/marketing/components/MarketingFeatureCard.vue")['default']
export const MarketingFeaturesGrid: typeof import("../app/features/marketing/components/MarketingFeaturesGrid.vue")['default']
export const MarketingFooter: typeof import("../app/features/marketing/components/MarketingFooter.vue")['default']
export const MarketingHeroSection: typeof import("../app/features/marketing/components/MarketingHeroSection.vue")['default']
export const MarketingNavbar: typeof import("../app/features/marketing/components/MarketingNavbar.vue")['default']
export const MarketingOnboardingStep: typeof import("../app/features/marketing/components/MarketingOnboardingStep.vue")['default']
export const MarketingOnboardingSteps: typeof import("../app/features/marketing/components/MarketingOnboardingSteps.vue")['default']
export const DailySummary: typeof import("../app/features/reservations/components/DailySummary.vue")['default']
export const ReservationsFilters: typeof import("../app/features/reservations/components/ReservationsFilters.vue")['default']
export const ReservationsHeader: typeof import("../app/features/reservations/components/ReservationsHeader.vue")['default']
export const ReservationsTable: typeof import("../app/features/reservations/components/ReservationsTable.vue")['default']
export const WeekStrip: typeof import("../app/features/reservations/components/WeekStrip.vue")['default']
export const RoomStatusBadge: typeof import("../app/features/rooms/components/RoomStatusBadge.vue")['default']
export const RoomTypeBadge: typeof import("../app/features/rooms/components/RoomTypeBadge.vue")['default']
export const RoomsFilters: typeof import("../app/features/rooms/components/RoomsFilters.vue")['default']
export const RoomsHeader: typeof import("../app/features/rooms/components/RoomsHeader.vue")['default']
export const RoomsStats: typeof import("../app/features/rooms/components/RoomsStats.vue")['default']
export const RoomsTable: typeof import("../app/features/rooms/components/RoomsTable.vue")['default']
export const RoleBadge: typeof import("../app/features/users/components/RoleBadge.vue")['default']
export const UserStatusBadge: typeof import("../app/features/users/components/UserStatusBadge.vue")['default']
export const UsersFilters: typeof import("../app/features/users/components/UsersFilters.vue")['default']
export const UsersHeader: typeof import("../app/features/users/components/UsersHeader.vue")['default']
export const UsersStats: typeof import("../app/features/users/components/UsersStats.vue")['default']
export const UsersTable: typeof import("../app/features/users/components/UsersTable.vue")['default']
export const NuxtWelcome: typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']
export const NuxtLayout: typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']
export const NuxtErrorBoundary: typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
export const ClientOnly: typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']
export const DevOnly: typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']
export const ServerPlaceholder: typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']
export const NuxtLink: typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']
export const NuxtLoadingIndicator: typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
export const NuxtTime: typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
export const NuxtRouteAnnouncer: typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
export const NuxtAnnouncer: typeof import("../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']
export const NuxtImg: typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
export const NuxtPicture: typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
export const NuxtPage: typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']
export const NoScript: typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']
export const Link: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']
export const Base: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']
export const Title: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']
export const Meta: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']
export const Style: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']
export const Head: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']
export const Html: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']
export const Body: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']
export const NuxtIsland: typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']
export const LazyAppHeader: LazyComponent<typeof import("../app/components/layout/AppHeader.vue")['default']>
export const LazyAppSidebar: LazyComponent<typeof import("../app/components/layout/AppSidebar.vue")['default']>
export const LazyLogo: LazyComponent<typeof import("../app/components/ui/Logo.vue")['default']>
export const LazyCreateCompanyForm: LazyComponent<typeof import("../app/features/auth/components/CreateCompanyForm.vue")['default']>
export const LazyCreateSuperAdminForm: LazyComponent<typeof import("../app/features/auth/components/CreateSuperAdminForm.vue")['default']>
export const LazyLoginForm: LazyComponent<typeof import("../app/features/auth/components/LoginForm.vue")['default']>
export const LazyReservationSettingsForm: LazyComponent<typeof import("../app/features/auth/components/ReservationSettingsForm.vue")['default']>
export const LazySetPasswordForm: LazyComponent<typeof import("../app/features/auth/components/SetPasswordForm.vue")['default']>
export const LazyType: LazyComponent<typeof import("../app/features/auth/type")['default']>
export const LazyAvailabilityGrid: LazyComponent<typeof import("../app/features/availability/components/AvailabilityGrid.vue")['default']>
export const LazyAvailabilityHeader: LazyComponent<typeof import("../app/features/availability/components/AvailabilityHeader.vue")['default']>
export const LazyAvailabilityLegend: LazyComponent<typeof import("../app/features/availability/components/AvailabilityLegend.vue")['default']>
export const LazyAvailabilityNav: LazyComponent<typeof import("../app/features/availability/components/AvailabilityNav.vue")['default']>
export const LazyAvailabilityStats: LazyComponent<typeof import("../app/features/availability/components/AvailabilityStats.vue")['default']>
export const LazyEquipmentAvailabilityGrid: LazyComponent<typeof import("../app/features/availability/components/EquipmentAvailabilityGrid.vue")['default']>
export const LazyTypes: LazyComponent<typeof import("../app/features/availability/types")['default']>
export const LazyCalendarWidget: LazyComponent<typeof import("../app/features/dashboard/components/CalendarWidget.vue")['default']>
export const LazyDashboardHeader: LazyComponent<typeof import("../app/features/dashboard/components/DashboardHeader.vue")['default']>
export const LazyMiniCalendar: LazyComponent<typeof import("../app/features/dashboard/components/MiniCalendar.vue")['default']>
export const LazyRecentActivity: LazyComponent<typeof import("../app/features/dashboard/components/RecentActivity.vue")['default']>
export const LazyReservationStatusBadge: LazyComponent<typeof import("../app/features/dashboard/components/ReservationStatusBadge.vue")['default']>
export const LazyRoomOccupancy: LazyComponent<typeof import("../app/features/dashboard/components/RoomOccupancy.vue")['default']>
export const LazyStatCard: LazyComponent<typeof import("../app/features/dashboard/components/StatCard.vue")['default']>
export const LazyStatsGrid: LazyComponent<typeof import("../app/features/dashboard/components/StatsGrid.vue")['default']>
export const LazyTodoWidget: LazyComponent<typeof import("../app/features/dashboard/components/TodoWidget.vue")['default']>
export const LazyUpcomingReservations: LazyComponent<typeof import("../app/features/dashboard/components/UpcomingReservations.vue")['default']>
export const LazyEquipmentStateBadge: LazyComponent<typeof import("../app/features/equipments/components/EquipmentStateBadge.vue")['default']>
export const LazyEquipmentsFilters: LazyComponent<typeof import("../app/features/equipments/components/EquipmentsFilters.vue")['default']>
export const LazyEquipmentsHeader: LazyComponent<typeof import("../app/features/equipments/components/EquipmentsHeader.vue")['default']>
export const LazyEquipmentsStats: LazyComponent<typeof import("../app/features/equipments/components/EquipmentsStats.vue")['default']>
export const LazyEquipmentsTable: LazyComponent<typeof import("../app/features/equipments/components/EquipmentsTable.vue")['default']>
export const LazyUsageTypeBadge: LazyComponent<typeof import("../app/features/equipments/components/UsageTypeBadge.vue")['default']>
export const LazyMarketingCallToAction: LazyComponent<typeof import("../app/features/marketing/components/MarketingCallToAction.vue")['default']>
export const LazyMarketingDashboardPreview: LazyComponent<typeof import("../app/features/marketing/components/MarketingDashboardPreview.vue")['default']>
export const LazyMarketingFeatureCard: LazyComponent<typeof import("../app/features/marketing/components/MarketingFeatureCard.vue")['default']>
export const LazyMarketingFeaturesGrid: LazyComponent<typeof import("../app/features/marketing/components/MarketingFeaturesGrid.vue")['default']>
export const LazyMarketingFooter: LazyComponent<typeof import("../app/features/marketing/components/MarketingFooter.vue")['default']>
export const LazyMarketingHeroSection: LazyComponent<typeof import("../app/features/marketing/components/MarketingHeroSection.vue")['default']>
export const LazyMarketingNavbar: LazyComponent<typeof import("../app/features/marketing/components/MarketingNavbar.vue")['default']>
export const LazyMarketingOnboardingStep: LazyComponent<typeof import("../app/features/marketing/components/MarketingOnboardingStep.vue")['default']>
export const LazyMarketingOnboardingSteps: LazyComponent<typeof import("../app/features/marketing/components/MarketingOnboardingSteps.vue")['default']>
export const LazyDailySummary: LazyComponent<typeof import("../app/features/reservations/components/DailySummary.vue")['default']>
export const LazyReservationsFilters: LazyComponent<typeof import("../app/features/reservations/components/ReservationsFilters.vue")['default']>
export const LazyReservationsHeader: LazyComponent<typeof import("../app/features/reservations/components/ReservationsHeader.vue")['default']>
export const LazyReservationsTable: LazyComponent<typeof import("../app/features/reservations/components/ReservationsTable.vue")['default']>
export const LazyWeekStrip: LazyComponent<typeof import("../app/features/reservations/components/WeekStrip.vue")['default']>
export const LazyRoomStatusBadge: LazyComponent<typeof import("../app/features/rooms/components/RoomStatusBadge.vue")['default']>
export const LazyRoomTypeBadge: LazyComponent<typeof import("../app/features/rooms/components/RoomTypeBadge.vue")['default']>
export const LazyRoomsFilters: LazyComponent<typeof import("../app/features/rooms/components/RoomsFilters.vue")['default']>
export const LazyRoomsHeader: LazyComponent<typeof import("../app/features/rooms/components/RoomsHeader.vue")['default']>
export const LazyRoomsStats: LazyComponent<typeof import("../app/features/rooms/components/RoomsStats.vue")['default']>
export const LazyRoomsTable: LazyComponent<typeof import("../app/features/rooms/components/RoomsTable.vue")['default']>
export const LazyRoleBadge: LazyComponent<typeof import("../app/features/users/components/RoleBadge.vue")['default']>
export const LazyUserStatusBadge: LazyComponent<typeof import("../app/features/users/components/UserStatusBadge.vue")['default']>
export const LazyUsersFilters: LazyComponent<typeof import("../app/features/users/components/UsersFilters.vue")['default']>
export const LazyUsersHeader: LazyComponent<typeof import("../app/features/users/components/UsersHeader.vue")['default']>
export const LazyUsersStats: LazyComponent<typeof import("../app/features/users/components/UsersStats.vue")['default']>
export const LazyUsersTable: LazyComponent<typeof import("../app/features/users/components/UsersTable.vue")['default']>
export const LazyNuxtWelcome: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']>
export const LazyNuxtLayout: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
export const LazyNuxtErrorBoundary: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
export const LazyClientOnly: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']>
export const LazyDevOnly: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']>
export const LazyServerPlaceholder: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const LazyNuxtLink: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']>
export const LazyNuxtLoadingIndicator: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
export const LazyNuxtTime: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
export const LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
export const LazyNuxtAnnouncer: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']>
export const LazyNuxtImg: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
export const LazyNuxtPicture: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
export const LazyNuxtPage: LazyComponent<typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']>
export const LazyNoScript: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']>
export const LazyLink: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']>
export const LazyBase: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']>
export const LazyTitle: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']>
export const LazyMeta: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']>
export const LazyStyle: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']>
export const LazyHead: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']>
export const LazyHtml: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']>
export const LazyBody: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']>
export const LazyNuxtIsland: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']>

export const componentNames: string[]
