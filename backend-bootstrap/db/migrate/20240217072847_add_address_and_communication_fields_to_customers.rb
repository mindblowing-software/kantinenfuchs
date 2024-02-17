class AddAddressAndCommunicationFieldsToCustomers < ActiveRecord::Migration[7.1]
  def change
    add_column :customers, :street, :string
    add_column :customers, :house_number, :string
    add_column :customers, :zip_code, :string
    add_column :customers, :city, :string
    add_column :customers, :contact_email, :string
    add_column :customers, :contact_phone, :string
  end
end
