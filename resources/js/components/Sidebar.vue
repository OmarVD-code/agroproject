<template>
  <!-- Sidebar -->
  <div
    :class="[
      'px-3 sidebar-content',
      { compressed: isCompressed && !isHovered },
    ]"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <div
      class="d-flex"
      :class="
        isCompressed && !isHovered
          ? 'justify-content-around'
          : 'justify-content-between'
      "
    >
      <h4 v-if="!isCompressed || isHovered">MENÚ</h4>
      <i
        class="fa-solid mt-1 sidebar-pin"
        :class="isCompressed ? 'fa-thumbtack' : 'fa-thumbtack-slash'"
        @click="toggleSidebar"
      ></i>
    </div>
    <!-- Items del sidebar -->
    <ul class="list-unstyled sidebar-items">
      <li
        v-for="item in items"
        :key="item.label"
        :class="{ active: isActive(item.path) }"
      >
        <router-link
          :to="item.path"
          class="d-flex align-items-center router-link"
        >
          <feather-icon :name="item.icon" size="25" class="mr-3" />
          <span v-if="!isCompressed || isHovered">{{ item.label }}</span>
        </router-link>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "SidebarComponent",
  data() {
    return {
      isCompressed: false,
      isHovered: false,
      items: [
        { label: "INVENTARIO", icon: "box", path: "/products" },
        { label: "VENTAS", icon: "dollar-sign", path: "/sales" },
        { label: "CLIENTES", icon: "users", path: "/customers" },
        { label: "PROVEEDORES", icon: "truck", path: "/suppliers" },
        { label: "REPORTES", icon: "file-plus", path: "/reports" },
      ],
    };
  },
  methods: {
    toggleSidebar() {
      this.isCompressed = !this.isCompressed;
    },
    handleMouseEnter() {
      this.isHovered = true; // Expande el sidebar al pasar el mouse
    },
    handleMouseLeave() {
      this.isHovered = false; // Comprime el sidebar si el pin no está activado
    },
    isActive(path) {
      return this.$route.path === path; // coincidencia exacta
    },
  },
};
</script>

<style scoped>
/* .sidebar-content {
  max-width: 250px;
  height: calc(
    100vh - 55.98px
  );
  overflow: hidden;
  transition: max-width 0.3s ease-in-out;
  left: 0;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  padding: 20px;
}

.sidebar-content.compressed {
  max-width: 60px;
}

.sidebar-content.compressed .sidebar-items li {
  justify-content: center;
}

.sidebar-content.compressed .mr-3 {
  margin-right: 0;
}

.sidebar-pin {
  cursor: pointer;
}

.sidebar-items {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-items li {
  padding: 10px 0;
  font-size: 20px;
  cursor: pointer;
}

.router-link {
  color: #fff;
  text-decoration: none;
}

.sidebar-items li:hover {
  transition: transform 0.2s ease-in-out;
  transform: translateX(10px);
}

.sidebar-items li.active {
  background: rgba(255, 255, 255, 0.12);
  border-radius: 10px;
}

.sidebar-items li.active .router-link {
  color: #7ee787;
  font-weight: 700;
} */
</style>
