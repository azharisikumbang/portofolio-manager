<template>
	<div class="bg-gray-200 min-h-screen">
		<div class="max-w-6xl mx-auto w-full rounded-lg pb-16">
			<ul class="flex cursor-pointer pt-8">
				<li 
					v-for="(step, index) in steps"
					v-bind:key="step.component"
					v-on:click="currentStep = step.component, nextStepOrder = index + 1"
					class='py-2 px-6 bg-white rounded-t-lg'
					:class="{ 'text-gray-500 bg-gray-200' : currentStep !== step.component }">
					{{ step.title }}
				</li>
			</ul>
			<component v-bind:is="currentStepComponent" class="shadow-xl" :key="nextStepOrder"></component>
			<button 
				v-if="currentStep !== 'Welcome'" 
				v-on:click="nextStep" 
				class="w-full bg-green-600 py-4 text-white hover:bg-green-700 rounded-br-lg rounded-bl-lg shadow-xl">
				{{ nextText }}
			</button>
		</div>
		
	</div>
</template>

<script>
    import BreezeGuestLayout from "@/Layouts/Guest"
    import Welcome from "@/components/install/Welcome"
    import SiteConfiguration from "@/components/install/SiteConfiguration"
    import PersonalInformation from "@/components/install/PersonalInformation"
    import Education from "@/components/install/Education"
    import WorkExperience from "@/components/install/WorkExperience"
    import SkillSet from "@/components/install/SkillSet"
    import Certification from "@/components/install/Certification"
    // import Loader from "@/components/Loader"

    export default {
        layout: BreezeGuestLayout,
        components: {
            Welcome,
            SiteConfiguration,
            PersonalInformation,
            Education,
            WorkExperience,
            SkillSet,
            Certification,
            // Loader,
        },
        data : function() { 
        	return {
        		currentStep: "Welcome",
        		nextStepOrder: 1,
        		nextText: "Simpan dan Lanjutkan",
        		steps: [
        			{
        				title: "Welcome",
        				component: "Welcome",
        			},
        			{
        				title: "Site Configuration",
        				component: "SiteConfiguration",
        			},
        			{
        				title: "Personal Information",
        				component: "PersonalInformation",
        			},
        			{
        				title: "Education",
        				component: "Education",
        			},
        			{
        				title: "Work Experience",
        				component: "WorkExperience",
        			},
        			{
        				title: "Skill Set",
        				component: "SkillSet",
        			},
        			{
        				title: "Certification",
        				component: "Certification",
        			},
        		]
        	}
        },
        computed: {
        	currentStepComponent : function() {
        		// this.nextText = "Simpan dan Lanjutkan"
        		return this.currentStep 
        	}
        },
        methods: {
        	nextStep : function() {
        		// this.loader = true
        		this.nextText = 'Menyimpan...'

        		// dp something

        		// loadnext on success
        		this.currentStep = this.steps[this.nextStepOrder].component
        		this.nextStepOrder++
        		this.nextText = 'Simpan dan Lanjutkan'
        	}
        }
    }
</script>
