class DashboardController < ApplicationController

  def show
    @today_orders = Order.where(ordered_at: Date.today)
    @today_sum = @today_orders.sum("number")
  end
end
