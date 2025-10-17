<template>
    <Head title="Property Search">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
        <div class="property-search-container">
            <el-card class="search-card">
                <template #header>
                    <div class="card-header">
                        <span class="title">Property Search</span>
                    </div>
                </template>

                <el-form :model="filters" label-width="auto" class="search-form">
                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="24" :md="24">
                            <el-form-item label="Property Name">
                                <el-input
                                    v-model="filters.name"
                                    placeholder="Search by name..."
                                    clearable
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="24" :md="8">
                            <el-form-item label="Bedrooms">
                                <el-input-number
                                    v-model="filters.bedrooms"
                                    :min="0"
                                    :max="20"
                                    clearable
                                    placeholder="Any"
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>

                        <el-col :xs="24" :sm="12" :md="8">
                            <el-form-item label="Bathrooms">
                                <el-input-number
                                    v-model="filters.bathrooms"
                                    :min="0"
                                    :max="20"
                                    clearable
                                    placeholder="Any"
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>

                        <el-col :xs="24" :sm="12" :md="8">
                            <el-form-item label="Storeys">
                                <el-input-number
                                    v-model="filters.storeys"
                                    :min="0"
                                    :max="20"
                                    clearable
                                    placeholder="Any"
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="12" :md="8">
                            <el-form-item label="Garages">
                                <el-input-number
                                    v-model="filters.garages"
                                    :min="0"
                                    :max="20"
                                    clearable
                                    placeholder="Any"
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>

                        <el-col :xs="24" :sm="12" :md="8">
                            <el-form-item label="Min. Price">
                                <el-input-number
                                    v-model="filters.price_min"
                                    :min="0"
                                    placeholder="Any"
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>

                        <el-col :xs="24" :sm="12" :md="8">
                            <el-form-item label="Max. Price">
                                <el-input-number
                                    v-model="filters.price_max"
                                    :min="0"
                                    placeholder="Any"
                                    @keyup.enter="search"
                                />
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="24" :md="24" class="button-col">
                            <el-button
                                type="primary"
                                :icon="Search"
                                :loading="loading"
                                class="search-button"
                                @click="search"
                            >
                                Search
                            </el-button>
                            <el-button
                                type="default"
                                :icon="Close"
                                @click="resetFilters"
                                class="reset-button">
                                Reset
                            </el-button>
                        </el-col>
                    </el-row>
                </el-form>
            </el-card>

            <el-card v-if="searched" class="results-card">
                <template #header>
                    <div class="card-header">
                        <span class="title">
                          Results
                          <el-tag v-if="results.length > 0" type="success">{{ results.length }}</el-tag>
                        </span>
                    </div>
                </template>

                <el-skeleton v-if="loading" :rows="5" animated />

                <el-empty
                    v-else-if="results.length === 0"
                    description="No properties found matching your criteria"
                    :image-size="150"
                />

                <!-- Results Table -->
                <el-table
                    v-else
                    :data="results"
                    stripe
                    style="width: 100%"
                    :default-sort="{ prop: 'price', order: 'ascending' }"
                >
                    <el-table-column prop="name" label="Property Name" min-width="175" sortable />
                    <el-table-column prop="bedrooms" label="Bedrooms" width="120" align="center" sortable />
                    <el-table-column prop="bathrooms" label="Bathrooms" width="130" align="center" sortable />
                    <el-table-column prop="storeys" label="Storeys" width="120" align="center" sortable />
                    <el-table-column prop="garages" label="Garages" width="120" align="center" sortable />
                    <el-table-column prop="price" label="Price" width="140" align="right" sortable>
                        <template #default="{ row }">
              <span class="price-tag">
                ${{ parseFloat(row.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
              </span>
                        </template>
                    </el-table-column>
                </el-table>
            </el-card>
        </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Search, Close } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import type { Property } from '@/types'
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    initialResults?: Property[]
}>()

const filters = ref({
    name: '',
    bedrooms: null as number | null,
    bathrooms: null as number | null,
    storeys: null as number | null,
    garages: null as number | null,
    price_min: null as number | null,
    price_max: null as number | null,
})

const results = ref<Property[]>([])
const loading = ref(false)
const searched = ref(false)

const search = async () => {
    if (
        filters.value.price_min !== null &&
        filters.value.price_max !== null &&
        filters.value.price_min > filters.value.price_max
    ) {
        ElMessage.error('Min price cannot be greater than max price')
        return
    }

    loading.value = true
    searched.value = true

    try {
        const params = new URLSearchParams()

        Object.entries(filters.value).forEach(([key, value]) => {
            if (value !== '' && value !== null && value !== undefined) {
                params.append(key, value.toString())
            }
        })

        const response = await fetch(`/api/v1/properties/search?${params}`)

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        results.value = await response.json()

        if (results.value.length > 0) {
            ElMessage.success(`Found ${results.value.length} properties`)
        }
    } catch (error) {
        console.error('Search failed:', error)
        ElMessage.error('Failed to search properties. Please try again.')
        results.value = []
    } finally {
        loading.value = false
    }
}

const resetFilters = () => {
    filters.value = {
        name: '',
        bedrooms: null,
        bathrooms: null,
        storeys: null,
        garages: null,
        price_min: null,
        price_max: null,
    }
    results.value = []
    searched.value = false
    ElMessage.info('Filters reset')
}
</script>

<style scoped>
.property-search-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f7fa;
    min-height: 100vh;
}

.search-card {
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0;
}

.title {
    font-size: 18px;
    font-weight: 600;
    color: #303133;
}

.search-form {
    padding: 10px 0;
}

.button-col {
    display: flex;
    align-items: flex-end;
}

.search-button {
    width: 100%;
}

.reset-button {
    width: 100%;
    margin-left: 10px;
}

.results-card {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.price-tag {
    color: #409eff;
    font-weight: 600;
}

@media (max-width: 768px) {
    .property-search-container {
        padding: 10px;
    }

    .reset-button {
        margin-left: 0;
        margin-top: 10px;
    }
}
</style>
