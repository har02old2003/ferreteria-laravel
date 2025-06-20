<template>
  <button
    :type="type"
    :class="[
      'inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background',
      sizeClasses[size],
      variantClasses[variant],
      iconOnly ? 'p-0' : '',
      className
    ]"
    :disabled="disabled || loading"
    v-bind="$attrs"
  >
    <component
      :is="iconLeft"
      v-if="iconLeft"
      :class="[
        'shrink-0',
        loading ? 'opacity-0' : 'opacity-100',
        iconOnly ? 'h-4 w-4' : 'h-4 w-4 mr-2'
      ]"
      aria-hidden="true"
    />
    
    <span
      :class="[
        loading ? 'opacity-0' : 'opacity-100',
        iconOnly ? 'sr-only' : ''
      ]"
    >
      <slot>{{ text }}</slot>
    </span>

    <svg
      v-if="loading"
      class="absolute h-4 w-4 animate-spin"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      />
    </svg>

    <component
      :is="iconRight"
      v-if="iconRight"
      :class="[
        'shrink-0',
        loading ? 'opacity-0' : 'opacity-100',
        'h-4 w-4 ml-2'
      ]"
      aria-hidden="true"
    />
  </button>
</template>

<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'ghost'].includes(value)
  },
  text: {
    type: String,
    default: ''
  },
  iconLeft: {
    type: [Object, Function],
    default: null
  },
  iconRight: {
    type: [Object, Function],
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  iconOnly: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})

const sizeClasses = {
  sm: 'h-8 px-3 text-xs',
  md: 'h-9 px-4 text-sm',
  lg: 'h-10 px-8 text-base'
}

const variantClasses = {
  primary: 'bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700',
  secondary: 'bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50',
  danger: 'bg-red-600 text-white hover:bg-red-700',
  ghost: 'hover:bg-gray-100 text-gray-900'
}
</script> 