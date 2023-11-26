class AddCustomerRefToUsers < ActiveRecord::Migration[7.1]
  def change
    add_reference :users, :customer, null: true, foreign_key: true
  end
end
