import type { Component } from 'vue'

export interface Feature {
  icon: Component
  title: string
  description: string
}

export interface OnboardingStepData {
  number: number
  icon: Component
  title: string
  description: string
}