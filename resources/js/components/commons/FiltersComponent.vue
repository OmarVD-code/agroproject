<template>
  <b-row class="d-flex justify-content-end">
    <b-col cols="12" class="mb-2">
      <b-form-group label="Desde" label-for="from" v-if="fromToFilter">
        <b-form-datepicker
          id="from"
          :date-format-options="{
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
          }"
        >
        </b-form-datepicker>
      </b-form-group>
    </b-col>
    <b-col cols="12" class="mb-2">
      <b-form-group label="Hasta" label-for="to" v-if="fromToFilter">
        <b-form-datepicker
          id="to"
          :date-format-options="{
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
          }"
        />
      </b-form-group>
    </b-col>
    <template v-for="filter in filters">
      <b-col
        :cols="filter.cols"
        :md="filter.md"
        class="mb-2"
        :key="filter.key"
        v-if="filter.visible"
      >
        <b-form-group :label="filter.label">
          <v-select
            :get-option-key="option => option.id"
            v-model="filter.model"
            :options="filter.options"
            :clearable="filter.clearable"
            :label="filter.labelSelect"
            :multiple="filter.multiple ? true : false"
            class="w-100"
            :reduce="(option) => option.id"
            @input="onChange"
          />
        </b-form-group>
      </b-col>
    </template>
  </b-row>
</template>

<script>
// import vSelect from "vue-select";

export default {
  props: {
    filters: Array,
    fromToFilter: {
      type: Boolean,
      default: true,
    },
    fromToObject: Object,
  },
  components: {
    // vSelect,
  },
  methods: {
    onChange(value) {
      console.log(value);
    },
  }
};
</script>

<style>

</style>