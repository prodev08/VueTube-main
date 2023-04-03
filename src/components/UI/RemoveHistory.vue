<template>
	<i class="fas fa-trash-alt" @click.prevent="removeHistory"></i>
</template>

<script>
// import { mapGetters } from 'vuex';

// import { useToast } from "vue-toastification";

export default {
	props: ['video'],
	data() {
		return {
			// toast: null,
		};
	},
	computed: {
		//...mapGetters(["getGoogleUser"])
	},
	created(){
		//this.toast = useToast();
	},
	methods: {
		removeHistory() {
			this.$store.dispatch('unHistory', { video: this.video });
			this.$emit('unHistory', this.video);
		},
	}
}
</script>

<style scoped>
	/* i.fa-heart {
		opacity: 1;
		transition: color 0.15s;
		color: var(--bs-light);
	}

	i.fa-heart.active {
		color: var(--red);
	} */
</style>