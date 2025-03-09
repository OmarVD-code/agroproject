<template>
  <b-form-select
    :options="finalOptions"
    v-model="current_status"
    @change="onChange"
    class="status-selector"
  />
</template>

<script>
export default {
  props: {
    status: {
      type: Number,
      default: 1,
      required: false,
    },
  },
  data() {
    return {
      options: [
        { value: 1, text: "PENDIENTE" },
        { value: 2, text: "CANCELADO" },
        { value: 3, text: "ANULADO" },
      ],
      current_status: this.status,
    };
  },
  computed: {
    finalOptions() {
      return this.options.filter((option) => option.value !== this.status);
    },
  },
  methods: {
    onChange(value) {
      this.$emit("change", value);
    },
  },
};
</script>

<style scoped>
.status-selector {
  max-width: 100px;
  height: 25px;
}
</style>