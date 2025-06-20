<template>
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <Line
            v-if="chartData.datasets[0].data.length > 0"
            :data="chartData"
            :options="chartOptions"
            class="h-[300px]"
        />
        <div v-else class="h-[300px] flex items-center justify-center border-2 border-dashed border-gray-200 rounded-lg">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-900">No hay datos disponibles</p>
                <p class="mt-2 text-sm text-gray-500">No se encontraron ventas para mostrar en el gráfico</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const props = defineProps({
    weekOffset: {
        type: Number,
        default: 0
    },
    salesData: {
        type: Array,
        required: true,
        default: () => []
    }
});

const chartData = computed(() => ({
    labels: props.salesData.map(day => day.date),
    datasets: [
        {
            label: 'Ventas Diarias (S/.)',
            data: props.salesData.map(day => day.total),
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            tension: 0.4
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => {
                    if (value >= 1000) {
                        return `S/ ${(value / 1000).toFixed(1)}k`;
                    }
                    return `S/ ${value.toLocaleString('es-PE')}`;
                }
            }
        },
        x: {
            grid: {
                display: false
            }
        }
    },
    plugins: {
        legend: {
            display: true,
            position: 'top'
        },
        tooltip: {
            callbacks: {
                label: (context) => `Ventas: S/ ${context.raw.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
            }
        }
    },
    elements: {
        point: {
            radius: 6,
            hoverRadius: 8
        },
        line: {
            borderWidth: 3
        }
    }
};
</script> 