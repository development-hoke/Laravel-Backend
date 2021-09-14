<template>
	<div>
		<v-container fluid grid-list-xl>
			<v-row>
				<v-col xl="6" lg="6" md="6" sm="7" cols="12">
					<today-weather></today-weather>
					<div class="welcome-message mb-8">
						<h4>Good morning, Jacqueline.</h4>
						<p>Here’s what’s happening with your store this week.</p>
					</div>
					<div class="welcome-chart">
						<v-row>
							<v-col xl="6" lg="6" md="6" sm="6" cols="12">
								<h2 class="mb-0 grey--text">$21,349.29</h2>
								<span class="d-block fs-12 mb-4 grey--text fw-normal">Earned Today</span>
								<bar-chart 
									:height="110"
									:dataSets="earnedToday"
								>
								</bar-chart>
							</v-col>
							<v-col xl="6" lg="6" md="6" sm="6" cols="12">
								<h2 class="mb-0 grey--text">15,800</h2>
								<span class="d-block fs-12 mb-4 grey--text fw-normal">Items Sold</span>
								<bar-chart 
									:height="110"
									:dataSets="itemsSold"
								>
								</bar-chart>
							</v-col>
						</v-row>
					</div>
				</v-col>
				<v-col xl="6" lg="6" md="6" cols="12" sm="5" class="d-sm-block d-none">
					<div class="d-custom-flex align-items-center justify-center pt-3">
						<img class="img-responsive" src="/static/img/agency-welcome.png" height="300" width="550" alt="agency block">
					</div>
				</v-col>
			</v-row>
			<v-row>
				<app-card
					:heading="$t('message.salesAndEarning')"
					:fullBlock="true"
					:reloadable="true"
					:fullScreen="true"
					:closeable="true"
					colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
					customClasses="sales-chart-widget"
				>	
					<div class="pa-4">
						<h2 class="mb-0">$35,455</h2>
						<p class="fs-12 grey--text fw-normal">Total Sales This Month</p>
					</div>
					<line-chart-with-area
						:color="salesAndEarning.color"
						:dataSet="salesAndEarning.data"
						:dataLabels="salesAndEarning.labels"
						:enableXAxesLine="false"
						:height="115"
					>
					</line-chart-with-area>
				</app-card>
				<v-col xl="6" lg="6" md="6" sm="12" cols="12">
					<v-row>
						<v-col xl="6" lg="6" md="6" sm="6" cols="12" class="col-height-auto">
							<app-card custom-classes="mb-5">
								<h6 class="mb-0">$2156</h6>
								<p class="fs-12 grey--text fw-normal">{{$t('message.totalEarnings')}}</p>
								<line-chart-shadow
									:dataSet="totalEarnings.data"
									:lineTension="totalEarnings.lineTension"
									:dataLabels="totalEarnings.labels"
									:width="370"
									:height="100"
									:borderColor="totalEarnings.borderColor"
									:enableGradient='false'
								>
								</line-chart-shadow>
							</app-card>
							<app-card>
								<h6 class="mb-0">$2156</h6>
								<p class="fs-12 grey--text fw-normal">{{$t('message.totalExpences')}}</p>
								<line-chart-shadow
									:dataSet="totalExpences.data"
									:lineTension="totalExpences.lineTension"
									:dataLabels="totalExpences.labels"
									:width="370"
									:height="100"
									:borderColor="totalExpences.borderColor"
									:enableGradient='false'
								>
								</line-chart-shadow>
							</app-card>
						</v-col>
						<v-col xl="6" lg="6" md="6" sm="6" cols="12" class="col-height-auto">
							<app-card custom-classes="mb-5">
								<h6 class="mb-0">$2156</h6>
								<p class="fs-12 grey--text fw-normal">{{$t('message.netProfit')}}</p>
								<line-chart-shadow
									:dataSet="netProfit.data"
									:lineTension="netProfit.lineTension"
									:dataLabels="netProfit.labels"
									:width="370"
									:height="100"
									:borderColor="netProfit.borderColor"
									:enableGradient='false'
								>
								</line-chart-shadow>
							</app-card>
							<app-card>
								<h6 class="mb-0">$2156</h6>
								<p class=" fs-12 grey--text fw-normal">{{$t('message.onlineRevenue')}}</p>
								<line-chart-shadow
									:dataSet="onlineRevenue.data"
									:lineTension="onlineRevenue.lineTension"
									:dataLabels="onlineRevenue.labels"
									:width="370"
									:height="100"
									:borderColor="onlineRevenue.borderColor"
									:enableGradient='false'
								>
								</line-chart-shadow>
							</app-card>
						</v-col>
					</v-row>
				</v-col>
			</v-row>
			<v-row>
				<app-card
					:heading="$t('message.newRequest')"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-12 col-sm-12"
					:fullBlock="true"
					:reloadable="true"
					:closeable="true"
					:fullScreen="true"
				>
					<users-list></users-list>
				</app-card>
				<app-card
					colClasses="col-xl-8 col-lg-8 col-md-8 col-12 col-sm-12"
					:heading="$t('message.projectStatus')"
					:fullBlock="true"
					:reloadable="true"
					:closeable="true"
					:fullScreen="true"
				>
					<project-status></project-status>
				</app-card>
			</v-row>
			<v-row>
				<app-card
					colClasses="col-xl-8 col-lg-8 col-md-7 col-12 col-sm-12"
					:heading="$t('message.adCampaignPerfomance')"
					:closeable="true"
					:fullScreen="true"
					:reloadable="true"
					contentCustomClass="pos-relative d-flex"
					customClasses="ad-campaign justify-space-between"
				>
					<ad-campaign-perfomance 
						style="width: 100%"
						:height="450" 
						:data="adCampaignPerfomanceData"
					></ad-campaign-perfomance>	
				</app-card>
				<app-card
					colClasses="col-xl-4 col-lg-4 col-md-5 col-12 col-sm-12"
					:fullBlock="true"
					customClasses="quote-wrap2"
				>
					<quote-of-the-day></quote-of-the-day>
				</app-card>
			</v-row>
			<v-row>
				<app-card
					colClasses="col-xl-4 col-lg-4 col-md-4 col-12 col-sm-6"
					:heading="$t('message.todoList')"
					:fullBlock="true"
					:reloadable="true"
					:closeable="true"
					:fullScreen="true"
				>
					<todo-list></todo-list>
				</app-card>
				<app-card
					:heading="$t('message.topSellingTheme')"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					:fullBlock="true"
					:reloadable="true"
					:closeable="true"
					:fullScreen="true"
					:footer="true"
				>
					<recent-sales></recent-sales>
					<div class="action" slot="footer">
						<v-btn small color="primary">{{$t('message.viewAll')}}</v-btn>
					</div>
				</app-card>
				<v-col xl="4" lg="4" md="4" sm="12" class="d-xs-full">
					<v-row>
						<app-card
							colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-12"
						>
							<new-order-countdown></new-order-countdown>
						</app-card>
						<app-card
							colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-12"
						>
							<followers></followers>
						</app-card>
						<app-card 
							:fullBlock="true"
							customClasses="booking-info-wrap"
							colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"
						>
							<booking-info></booking-info>
						</app-card>
					</v-row>
				</v-col>
			</v-row>
			<v-row>
				<app-card 
					:fullBlock="true"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 col-height-auto active-user-wrap"
				>
					<active-user :data="activeUser"></active-user>
					</app-card>
				<app-card
					:heading="$t('message.employeePayroll')"
					:withTabs="true"
					:tabs="[$t('message.lastMonth'), $t('message.allTime')]"
					:fullBlock="true"
					colClasses="col-xl-8 col-lg-8 col-md-8 col-sm-12 d-full"
				>
					<employee-payroll></employee-payroll>
				</app-card>
			</v-row>
			<v-row>
				<v-col xl="8" lg="8" md="8" sm="12" cols="12" class="col-height-auto">
					<v-row>
						<v-col xl="12" lg="12" md="12" cols="12">
							<mailbox></mailbox>
						</v-col>
					</v-row>
					<v-row>
						<app-card
							colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12"
							:heading="$t('message.newsletter')"
							:fullScreen="true"
							:reloadable="true"
							:closeable="true"
						>
							<news-letter-campaign 
								:height="350"
								:labels="['1', '2', '3', '4', '5', '6', '7']"
								:data1="[19,21,18,20,23,16,18,30]"
								:data2="[10,8,14,11,10,12,10,0]"
							></news-letter-campaign>
						</app-card>
						<app-card
							colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 col-height-auto"
							:fullBlock="true"
							class="top-author-wrap"
						>
							<top-authors></top-authors>
						</app-card>
					</v-row>
				</v-col>
				<v-col xl="4" lg="4" md="4" cols="12" sm="12">
					<v-row>
						<app-card
							colClasses="col-xl-12 col-lg-12 col-md-12 col-12 col-sm-6"
							:heading="$t('message.profitShare')"
							:closeable="true"
							:fullScreen="true"
							:reloadable="true"
							customClasses="profit-share-widget"
						>
							<profit-share></profit-share>			
						</app-card>
						<v-col xl="12" lg="12" md="12" sm="6" cols="12">
							<activity></activity>
						</v-col>
					</v-row>
				</v-col>
			</v-row>
			<v-row>
				<v-col xl="6" lg="6" md="6" sm="12" cols="12" class="pa-0">
					<chat></chat>
				</v-col>
				<v-col xl="6" lg="6" md="6" sm="12" cols="12">
					<event-calendar></event-calendar>
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script>
import BarChart from "Components/Charts/BarChart";
import LineChartWithArea from "Components/Charts/LineChartWithArea";
import LineChartShadow from "Components/Charts/LineChartShadow";
import AdCampaignPerfomance from "Components/Charts/AdCampaignPerfomance";
import NewsLetterCampaign from "Components/Charts/NewsLetterCampaign";

// widgets
import TodoList from "Components/Widgets/ToDoList";
import ProjectStatus from "Components/Widgets/ProjectStatus";
import UsersList from "Components/Widgets/UsersList";
import RecentSales from "Components/Widgets/RecentSales";
import QuoteOfTheDay from "Components/Widgets/QuoteOfTheDay";
import NewOrderCountdown from "Components/Widgets/NewOrderCountdown";
import Followers from "Components/Widgets/Followers";
import BookingInfo from "Components/Widgets/BookingInfo";
import EmployeePayroll from "Components/Widgets/EmployeePayroll";
import Mailbox from "Components/Widgets/Mailbox";
import ActiveUser from "Components/Widgets/ActiveUser";
import TopAuthors from "Components/Widgets/TopAuthors";
import Chat from "Components/Widgets/Chat";
import EventCalendar from "Components/Widgets/EventCalendar";
import Activity from "Components/Widgets/Activity";
import TodayWeather from "Components/Widgets/TodayWeather";
import ProfitShare from "Components/Widgets/ProfitShare";

// data
import {
  earnedToday,
  itemsSold,
  salesAndEarning,
  totalEarnings,
  netProfit,
  onlineRevenue,
  totalExpences,
  adCampaignPerfomanceData,
} from "./data";
import { activeUser } from "Views/widgets/data";

export default {
  components: {
		BarChart,
		LineChartWithArea,
		LineChartShadow,
		TodoList,
		ProjectStatus,
		UsersList,
		RecentSales,
		QuoteOfTheDay,
		NewOrderCountdown,
		Followers,
		BookingInfo,
		EmployeePayroll,
		AdCampaignPerfomance,
		Mailbox,
		ActiveUser,
		TopAuthors,
		Chat,
		EventCalendar,
		Activity,
		NewsLetterCampaign,
		TodayWeather,
		ProfitShare
  },
  data() {
    return {
      earnedToday,
      itemsSold,
      salesAndEarning,
      totalEarnings,
      netProfit,
      onlineRevenue,
      totalExpences,
      adCampaignPerfomanceData,
      activeUser
    };
  }
};
</script>

