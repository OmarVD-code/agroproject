<template>
  <span
    v-html="svg"
    :class="[iconClass, $attrs.class]"
    @click="$emit('click', $event)"
    @mouseover="$emit('mouseover', $event)"
    @mouseout="$emit('mouseout', $event)"
  />
</template>

<script>
import feather from "feather-icons";

export default {
  name: "FeatherIcon",
  props: {
    name: {
      type: String,
      required: true, // Nombre del ícono, como "home", "camera", etc.
    },
    size: {
      type: [String, Number],
      default: 24, // Tamaño del ícono (24px por defecto)
    },
    iconClass: {
      type: String,
      default: "", // Clases CSS adicionales para estilizar el ícono
    },
    clickable: {
      type: Boolean,
      default: false, // Permite hacer clic en el ícono
    },
  },
  computed: {
    svg() {
      return feather.icons[this.name]
        ? feather.icons[this.name].toSvg({
            width: this.size,
            height: this.size,
            cursor: this.clickable ? "pointer" : "default",
          })
        : `<svg><!-- Icon not found --></svg>`;
    },
  },
};
</script>

<style scoped>
/* Estilo base para íconos */
</style>
